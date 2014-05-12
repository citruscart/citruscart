<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class CitruscartCSV extends JObject
{

	/*
	 * Parses content from a file into an array
	 * A field containing integer or fload doesnt need to be escaped in double-qoutes
	 *
	 * @param $content 						String to be translated
	 * @param $fields 						Array of indexes fields which we want to process (an empty array means we want to process all fields)
	 * @param $num_fields 				Number of fields in a row (0 means that it'll be calculated from the first row -> header)
	 * @param $method 						Method to use to parse the data (1 - explode, 2 - our own (more complex and slower) method)
	 * @param $params							Parameters of importing
	 * 			- skip_first 				If first line of the content should be skipped (not parsed as a record - usually a header)
	 *      - preserve_header		Preserve header as a firt row of the result array
	 * 			- rec_deliminer 		Delimier distinguishing records from each other (for method 2, if it's  it can be used also in field content)
	 * 			- field_deliminer 	Deliminer distinguishing fields in a record
	 * 			- clear_fields 			If we want to get rid of double quotes in string-containing fields
	 * 			- preserve_indexes 	If we want to have the same field indexes in result array as in the CSV file
	 * 			- begin_import			Starting with importing right now? (true by default - important to set to false when importing next time using offset)
	 * 			- throttled_import	Are we performing th throttled import ? (return data + offset after reading)
	 * 			- num_records			  Max number of loaded records, if needed to be limited (for throttled import ) - 0 => unlimited
	 * 			- offset						Offset in parsing file
	 * 			- chunk_size			  Size of one chunk of data read during throttled import(required only when number of records is set)
	 *
	 * @return Returns array of arrays representing records (throttled import => array( results, new offset ))
	 */
	function toArray( $content, $fields = array(), $num_fields = 0, $method = 1,  $params = '' )
	{
		$result = array();
		switch( $method )
		{
			case 1 : // explode method
				$result = CitruscartCSV::toArrayExplode( $content, $fields, $num_fields, $params );
				break;
			case 2 : // our own method
				$result = CitruscartCSV::toArrayOur( $content, $fields, $num_fields, $params );
				break;
		}
		return $result;
	}

	/*
	 * Parses content from a file into an array using explode function
	 * A field containing integer or fload doesnt need to be escaped in double-qoutes
	 *
	 * @param $content 						String to be translated
	 * @param $fields 						Array of indexes fields which we want to process (an empty array means we want to process all fields)
	 * @param $num_fields 				Number of fields in a row (0 means that it'll be calculated from the first row -> header)
	 * @param $params     				Parameters of importing
	 * 			- skip_first 				If first line of the content should be skipped (not parsed as a record - usually a header)
	 *      - preserve_header		Preserve header as a firt row of the result array
	 * 			- rec_deliminer 		Delimier distinguishing records from each other (for method 2, if it's  it can be used also in field content)
	 * 			- field_deliminer 	Deliminer distinguishing fields in a record
	 * 			- clear_fields 			If we want to get rid of double quotes in string-containing fields
	 * 			- preserve_indexes 	If we want to have the same field indexes in result array as in the CSV file
	 * 			- begin_import			Starting with importing right now? (true by default - important to set to false when importing next time using offset)
	 * 			- throttled_import	Are we performing th throttled import ? (return data + offset after reading)
	 * 			- num_records			  Max number of loaded records, if needed to be limited (for throttled import ) - 0 => unlimited
	 * 			- offset						Offset in parsing file
	 * 			- chunk_size			  Size of one chunk of data read during throttled import(required only when number of records is set)
	 *
	 * @return Returns array of arrays representing records (throttled import => array( results, new offset ))
	 */
	function toArrayExplode( $content, $fields = array(), $num_fields = 0, $params = '' )
	{
		if( !$params )
			$params = new DSCParameter();

		$skip_first = $params->getValue( 'skip_first', true );
		$preserve_header = $params->getValue( 'preserve_header', false );
		$rec_deliminer = $params->getValue( 'rec_deliminer', "\n" );
		$field_deliminer = $params->getValue( 'field_deliminer', "," );
		$clear_fields = $params->getValue( 'clear_fields', true );
		$preserve_indexes = $params->getValue( 'preserve_indexes', true );
		$num_records = $params->getValue( 'num_records', 0 );
		$throttled = $params->getValue( 'throttled_import', false );
		$offset_original = $offset = $params->getValue( 'offset', 0 );
		$begin_import = $params->getValue( 'begin_import', true );
		$chunk = $params->getValue( 'chunk_size', 4096 );

		$recs = 0; // number of read recs
		$result = array(); // array with results
		$offset_act = 0; // relative offset in the file
		$size_deliminer = strlen( $rec_deliminer ); // size of deliminer (so it does not need to be calculated every time)

		if( $throttled ) // not all records might be imported
		{
			$read_records = $num_records; // read only $num_records records
			jimport( 'joomla.filesystem.file' );
			$source_file = $content;
			$content = file_get_contents( $source_file, false, $chunk, $chunk, $offset ); // read the first chunk of data
		}
		$tmp = explode( $rec_deliminer, $content );
		if( !$num_records ) // all records should be read
		{
			if( $throttled )
				$num_records = -1;
			else
				$num_records = count( $tmp );
		}

		if( !$tmp || !$num_records ) // no results or a deliminer is empty => empty array
			return $result;

		if( !$num_fields ) // number of fields is not set => get it from header (first line)
			$num_fields = count( explode( $field_deliminer, $tmp[0] ) );

		$c = count( $tmp ); // number of currently loaded records
		$i = 0; // index in the list of read records from the file
		$rec_string = ''; // string containing the current record
		$rec_size = 0;
		$act_rec = false;
		$last_in_chunk = false; // if the record was last in the chunk (needs to check if the whole record ended in that chunk)
		while( $recs != $num_records )
		{
			$length = strlen( $tmp[$i] );
			$offset_act += $length;
			if( $i < ( $c - 1 ) )
				$offset_act += $size_deliminer;

			// read actual "record" from the file (in case of throttled import - we dont know if this is the whole record)
			if( strlen( $rec_string ) ) // append the "record" if there are traces of the previous "record" (in case of abrupt end of the chunk)
				$rec_string .= $tmp[$i];
			else // new record
				$rec_string = $tmp[$i];

			if( $length )
			{
				$act_data = explode( $field_deliminer, $rec_string );
				$act_rec = CitruscartCSV::processFieldsToArray( $fields, $act_data, $clear_fields && !$throttled, $preserve_indexes );
				if( count( $act_data ) == $num_fields ) // whole record
				{
					if( $begin_import )
					{
						$begin_import = false; // the first record was read
						if( $skip_first ) // we want to skip the first record
						{
							if( $preserve_header ) // but we want to keep it because it's header
								$result[] = $act_rec;
							$act_rec = false;

							if( !$throttled ) // not a throttled import -> one record represents header
								$num_records--;
						}
					}

					if( !$last_in_chunk ) // tha previously processed record was not the last loaded record in the chunk
					{
						if( $i != ( $c - 1) ) // this one is not the last loaded record in the current chunk so the record is fully parsed
							$rec_string = '';
					}
					else // the previously processed record was the last loaded record in the chunk
					{
						// we update it
						$result[$recs] = $act_rec;
						$act_rec = false; // nothing new will be added to array of results
						if( $i != ( $c - 1) )  // this one is not the last loaded record in the current chunk so the record is fully parsed
						{
							$recs++;
							$rec_string = '';
						}
					}
				}
				else // only part of the record
				{
					if( $last_in_chunk ) // the previously processed record was the last loaded record in the chunk
					{
						if( $i != ( $c - 1) ) // not the last in the chunk again
						{
							// we update it and set this record as "not the last in the chunk"
							$result[$recs] = $act_rec;
							$recs++;
							$rec_string = '';
							$last_in_chunk = false;
						}
					}
					$act_rec = false; // nothing is added to the array of results
				}
			}
			else // no record was read
			{
				if( strlen( $rec_string ) ) // this is the end of the currently processed record so we need to increase number of added records (it was decreased, because this record is divided among several chunks)
					$recs++;

				$last_in_chunk = false;
				$act_rec = false;
				$rec_string = '';
			}

			if( $act_rec ) // if there is a record waiting to be added -> add it
			{
				$result []= $act_rec;
				$recs++;
			}
			$i++; // next "record"

			if( ( $i == $c ) && $throttled ) // we need to load another chunk
			{
				$last_in_chunk = true;
				if( $act_rec )
					$recs--;

				$offset += $chunk;
				$content = file_get_contents( $source_file, false, $chunk, $chunk, $offset ); // read the next chunk of data
				if( $content == false ) // end of file? get out of the cycle
					break;
				$tmp = explode( $rec_deliminer, $content );
				$i = 0;
				$c = count( $tmp );
			}
		}

		if( $throttled )
		{
			if( $clear_fields ) // clear fields -> this is a slow solution, but it's coded fast
			{
				for( $i = 0, $c = count( $result ) ; $i < $c; $i++ )
				{
					$result[$i] = CitruscartCSV::processFieldsToArray( $fields, $result[$i], true, $preserve_indexes );
				}
			}
			return array( $result, $offset_original + $offset_act );
		}

		return $result;
	}

	/*
	 * Parses content from a file into an array using our own function
	 * A field containing integer or fload doesnt need to be escaped in double-qoutes
	 *
	 * @param $content 						String to be translated
	 * @param $num_fields 				Number of fields in a row (0 means that it'll be calculated from the first row -> header)
	 * @param $fields 						Array of indexes fields which we want to process (an empty array means we want to process all fields)
	 * @param $params 						Parameters of importing
	 * 			- skip_first 				If first line of the content should be skipped (not parsed as a record - usually a header)
	 *      - preserve_header		Preserve header as a firt row of the result array
	 * 			- rec_deliminer 		Delimier distinguishing records from each other (for method 2, if it's  it can be used also in field content)
	 * 			- field_deliminer 	Deliminer distinguishing fields in a record
	 * 			- clear_fields 			If we want to get rid of double quotes in string-containing fields
	 * 			- preserve_indexes 	If we want to have the same field indexes in result array as in the CSV file
	 * 			- begin_import			Starting with importing right now? (true by default - important to set to false when importing next time using offset)
	 * 			- throttled_import	Are we performing th throttled import ? (return data + offset after reading)
	 * 			- num_records			  Max number of loaded records, if needed to be limited (for throttled import ) - 0 => unlimited
	 * 			- offset						Offset in parsing file
	 * 			- chunk_size			  Size of one chunk of data read during throttled import(required only when number of records is set)
	 *
	 * @return Returns array of arrays representing records (throttled import => array( results, new offset ))
	 */
	function toArrayOur( $content, $fields = array(), $num_fields = 0, $params = '' )
	{
		if( !$params )
			$params = new DSCParameter();

		$skip_first = $params->getValue( 'skip_first', true );
		$preserve_header = $params->getValu( 'preserve_header', false );
		$rec_deliminer = $params->getValue( 'rec_deliminer', "\n" );
		$field_deliminer = $params->getValue( 'field_deliminer', "," );
		$clear_fields = $params->getValue( 'clear_fields', true );
		$preserve_indexes = $params->getValue( 'preserve_indexes', true );
		$num_records = $params->getValue( 'num_records', 0 );
		$offset = $params->getValue( 'offset', 0 );
		$begin_import = $params->getValue( 'begin_import', true );
		$chunk = $params->getValue( 'chunk_size', 4096 ); // 4kB by default

		$result = array();
		$tmp_lines = explode( $rec_deliminer, $content );

		if( !$tmp_lines || ( !($c = count( $tmp_lines ) )) ) // no results or a deliminer is empty => empty array
			return $result;

		if( !$num_fields ) // number of fields is not set => get it from header (firt line)
			$num_fields = count( explode( $field_deliminer, $tmp_lines[0] ) );
		$c = count( $tmp_lines ); // number of records

		if( $skip_first ) // skip first line
		{
			$tmp_head = array_shift( $tmp_lines );
			if( $preserve_header ) // we want to preserve header
				$result[] = CitruscartCSV::processFieldsToArray( $fields, explode( $field_deliminer, $tmp_head ), $clear_fields, $preserve_indexes );

			$c--; // adjust number of records
		}

		$record = 0;
		for( $i = 0; $i < $c; $i++ )
		{
			if( !strlen( $tmp_lines[$i] ) ) // skip empty lines between records
				continue;

			$last_unclosed = false;
			$tmp_arr1 = array();
			$tmp_arr2 = array();
			$c_act = 0;
			while($i < $c)
			{
				$tmp_arr2 = explode( $field_deliminer, $tmp_lines[$i] );
				$c2 = count( $tmp_arr2 );
				$j = 0;

				if( $last_unclosed ) // last field of previous line was unclosed
				{
					// try to find a field with odd number of double quotes first
					$tmp = array();
					while( ( $j < $c2 ) && ( substr_count( $tmp_arr2[$j], '"') % 2 != 1 ) ) $tmp[] = $tmp_arr2[$j++];
					$tmp_arr1[$c_act] .= $rec_deliminer.implode($field_deliminer, $tmp); // add them to the last field of previous line
				}

				if( $j == $c2 ) // the last field is still open :(
				{
					$last_unclosed = true;
					$i++;
					continue; // continue to the next line
				}
				else if( $last_unclosed )// the last field was successfully closed so we can move to the next field
				{
					$c_act++;
					$last_unclosed = false;
					$j++;
				}

				while( $j < $c2 ) // go through rest of fields
				{
					if( $last_unclosed ) // if the last field was unclosed
					{
						$tmp = array();
						$tmp[] = $tmp_arr2[$j++]; // first in the field is the current part

						while( ( $j < $c2 ) && ( substr_count( $tmp_arr2[$j], '"') % 2 != 1 ) ) // find another unclosed field
							$tmp[] = $tmp_arr2[$j++];
						if($j < $c2) // if we  found the end -> save it
							$tmp[] = $tmp_arr2[$j];

						if( @strlen($tmp_arr1[$c_act]) ) // add this part to the rest of the current field
							$tmp_arr1[$c_act] .= $row_deliminer.implode($field_deliminer, $tmp); // add the result to the current field
						else
							$tmp_arr1[$c_act] = implode($field_deliminer, $tmp); // add the result to the current field

						if( $j == $c2 ) // if we havent found any unclosed field until the end of this line, continue to the next line
							continue;

						// we found another unclosed field and matched it with the first one
						$j++;
						$last_unclosed = false;
						$c_act++; // continue to the next field
					}
					else
					{
						if( substr_count( $tmp_arr2[$j], '"') % 2 == 1 ) // unclosed field => look for the end of this field
						{
							$last_unclosed = true;
							continue;
						} // closed field so mark that we work with a closed field
						else
							$last_unclosed = false;

						// closed field => just copy it
						@$tmp_arr1[$c_act++] = $tmp_arr2[$j++];
					}
				}

				if( $c_act == $num_fields ) // we finished the record so we're good to go to parse a new record
					break;

				$i++; // otherwise, start parsing another line
			}

			$result[] = CitruscartCSV::processFieldsToArray( $fields, $tmp_arr1, $clear_fields, $preserve_indexes );
			$record++;
			if( $record == $num_records )
				break;
		}

		return $result;
	}

	/*
	 * Process fields while parsing CSV data:
	 * Cut out only fields we want to use
	 * Clear string-containing fields if we want so
	 *
	 * @param $fields Array of indexes of fields we want to process (an empty array means all fields)
	 * @param $data Array of all fields
	 * @param $clear_fields If we want to clear string-contaning fields
	 * @param $preserve_indexes If we want to have the same field indexes in result array as in the CSV file
	 *
	 * @return Array With cleaned up fields
	 */
	function processFieldsToArray( &$fields, $data, $clear_fields, $preserve_indexes )
	{
		$row = array();
		$c = count( $fields );
		$process_all = false; // process all
		if( !$c ) // array is empty
		{
			$c = count( $data );
			$process_all = true;// we want to process all fields
		}

		if( $process_all )
		{
			if( $clear_fields ) // clean out all fields
			{
				for($i = 0; $i < $c; $i++)
				{
					// cut off double quotation marks if there are any
					//if( isset( $data[$i] ) && strlen( $data[$i ]) && ( $data[$i][0]	 == '"' ) )
					if( isset( $data[$i] ) && strlen( $data[$i]))
					{
						//$row[$i] = substr( $data[$i], 1, strlen( $data[$i] )-2 );// what is this for?
						//$row[$i] = str_replace( '""', '"', $row[$i] ); // replace double double-quotes with only one double-quote
						$row[$i] = str_replace( '""', '"', $data[$i] ); // replace double double-quotes with only one double-quote
					}
					else // otherwise the value is float/integer
					{
						if( !isset( $data[$i] ) || !strlen($data[$i]) ) // the field is empty so empty it
							$row[$i] = '';
						else // otherwise it must be float or integer
						{
							$row[$i] = ( float )@$data[$i];
							if( !strcmp( ( int ) $row[$i], $row[$i] ) ) // the number is integer and not float
								$row[$i] = ( int )$row[$i];
						}
					}
				}
			}
			else // process all and we doesnt want to clear fields => return unchanged array
			{
				return $data;
			}
		}
		else // we process only part of the array
		{
			if( $clear_fields )
			{
				for($i = 0; $i < $c; $i++)
				{
						// cut off double quotation marks if there are any
						if( isset( $data[$fields[$i]] ) && strlen( $data[$fields[$i]] ) && ( $data[$fields[$i]][0]	 == '"' ) )
						{
							$row[$fields[$i]] = substr( $data[$fields[$i]], 1, strlen( $data[$fields[$i]] )-2 );
							$row[$fields[$i]] = str_replace( '""', '"', $row[$fields[$i]] ); // replace double double-quotes with only one double-quote
						}
						else // otherwise the value is float/integer
						{
							$idx = $preserve_indexes ? $fields[$i] : $i; // index in the result array
							if( !isset( $data[$fields[$i]] ) || !strlen($data[$fields[$i]]) ) // the field is empty so empty it
								$row[$idx] = '';
							else // otherwise it must be float or integer
							{
								if( empty( $data[$idx] ) )
									$row[$idx] = '';
								else
								{
									$row[$idx] = ( float )$data[$idx];
									if( !strcmp( ( int ) @$row[$i], @$row[$i] ) ) // the number is integer and not float
										$row[$idx] = ( int )@$row[$idx];
								}
							}
						}
				}
			}
			else // copy only requested fields
			{
				for($i = 0; $i < $c; $i++)
					$row[$preserve_indexes ? $fields[$i] : $i] = @$data[$fields[$i]];
			}
		}
		return $row;
	}

	/*
	 * Creates a CSV string from an array
	 *
	 * @param $content 					Array of records (in case it's not an array => return FALSE)
	 * @param $header 					Array of header fields
	 * @param $use_fields				Array of indexes of fields we want to export to CSV (an empty array means all fields)
	 * @param $field_deliminer 	Field deliminer
	 * @param $rec_deliminer 		Record deliminer
	 *
	 * @return 									CSV string
	 */
	function fromArray( $content, $header = array(), $use_fields = array(), $field_deliminer = ",", $rec_deliminer = "\n" )
	{
		if( !is_array( $content ) )
			return false;

		$result = '';
		if( count( $header ) ) // we want to export header too
			$result = CitruscartCSV::processFieldsFromArray( $header, $use_fields, $field_deliminer ).$rec_deliminer;

		for( $i = 0, $c = count( $content ); $i < $c; $i++ ) // export all other records
		{
			$result.= CitruscartCSV::processFieldsFromArray( $content[$i], $use_fields, $field_deliminer );
			if( $i < ( $c - 1 ) )
				$result .= $rec_deliminer;
		}

		return $result;
	}

	/*
	 * Processes fields to export to CSV format
	 *
	 * @param $fields 					Array of fields
	 * @param $use_fields				Array of indexes of fields we want to export to CSV (an empty array means all fields)
	 * @param $field_deliminer 	Field deliminer
	 *
	 * @return 									CSV string
	 */
	function processFieldsFromArray( &$fields, $use_fields = array(), $field_deliminer = ',' )
	{
		$result = '';

		$c = count($use_fields);
		if( $c ) // we specified fields to export
		{
			// get only wanted fields
			$wanted = array();
			for( $i = 0; $i < $c; $i++ )
				$wanted[] = $fields[$use_fields[$i]];

			$fields = $wanted;
		}
		else // all fields are processed
			$c = count( $fields );

		$db = JFactory::getDbo();
		// go through all fields and process them
		for($i = 0; $i < $c; $i++ )
		{
			if( !isset( $fields[$i] ) || !strlen( $fields[$i] ) ) // first of all, check if this property isnt empty
			{
				$result .= $field_deliminer;
				continue; // go to another field
			}

			// second step -> check, if the content is float/integer
			if( !strcmp( $fields[$i], ( int )$fields[$i] ) ) // the content is integer?
			{
				$result .= $field_deliminer.( int )$fields[$i];
				continue; // go to another field
			}
			else
				if( !strcmp( $fields[$i], ( float )$fields[$i] ) ) // the content is float?
				{
					$result .= $field_deliminer.( float )$fields[$i];
					continue; // go to another field
				}
			// as the last possibility -> it's a string
			// first of all -> double any double-quotes you find
			$fields[$i] = str_replace( '"','""', $fields[$i] );
			$result .= $field_deliminer.'"'.$fields[$i].'"';
		}
		return substr($result, strlen( $field_deliminer ) ); // cut off the first deliminer
	}

	/*
	 * Creates a CSV string from an array and saves it to a file
	 *
	 * @param $file_path				Path to file to save the CSV data
	 * @param $content 					Array of records (in case it's not an array => return FALSE)
	 * @param $header 					Array of header fields
	 * @param $use_fields				Array of indexes of fields we want to export to CSV (an empty array means all fields)
	 * @param $field_deliminer 	Field deliminer
	 * @param $rec_deliminer 		Record deliminer
	 *
	 * @return 									True on success
	 */
	function fromArrayToFile( $file_path, $content, $header = array(), $use_fields = array(), $field_deliminer = ",", $rec_deliminer = "\n" )
	{
		jimport( 'joomla.filesystem.file' );

		$buffer = CitruscartCSV::fromArray( $content, $header, $use_fields, $field_deliminer, $rec_deliminer ); // prepare CSV data

		return JFile::write( $file_path, $buffer ); // save the file
	}

	/*
	 * Parses content from a file into an array
	 * A field containing integer or fload doesnt need to be escaped in double-qoutes
	 *
	 * @param $file_path					Path to read the file from
	 * @param $fields 						Array of indexes fields which we want to process (an empty array means we want to process all fields)
	 * @param $num_fields 				Number of fields in a row (0 means that it'll be calculated from the first row -> header)
	 * @param $method 						Method to use to parse the data (1 - explode, 2 - our own (more complex and slower) method)
	 * @param $params							Parameters of importing
	 * 			- skip_first 				If first line of the content should be skipped (not parsed as a record - usually a header)
	 *      - preserve_header		Preserve header as a firt row of the result array
	 * 			- rec_deliminer 		Delimier distinguishing records from each other (for method 2, if it's  it can be used also in field content)
	 * 			- field_deliminer 	Deliminer distinguishing fields in a record
	 * 			- clear_fields 			If we want to get rid of double quotes in string-containing fields
	 * 			- preserve_indexes 	If we want to have the same field indexes in result array as in the CSV file
	 * 			- begin_import			Starting with importing right now? (true by default - important to set to false when importing next time using offset)
	 * 			- throttled_import	Are we performing th throttled import ? (return data + offset after reading)
	 * 			- num_records			  Max number of loaded records, if needed to be limited (for throttled import ) - 0 => unlimited
	 * 			- offset						Offset in parsing file
	 * 			- chunk_size			  Size of one chunk of data read during throttled import(required only when number of records is set)
	 *
	 * @return Returns array of arrays representing records (throttled import => array( results, new offset ))
	 */
	function fromFileToArray( $file_path, $fields = array(), $num_fields = 0, $method = 1, $params = '' )
	{
		if( empty( $params ) )
			$params = new DSCParameter();

		$throttled = $params->getValue( 'throttled_import', false );
		if( $throttled )
			$content = $file_path; // pass path to the file so we can access it in future
		else // parse whole file
			$content = file_get_contents( $file_path );// read the file
		return CitruscartCSV::toArray( $content, $fields, $num_fields, $method, $params );
	}
}
