/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only define the Joomla namespace if not defined.
if (typeof(Joomla) === 'undefined')
{
	var Joomla = {};
	/**
	 * Custom behavior for JavaScript I18N in Joomla! 1.6
	 *
	 * Allows you to call Joomla.JText._() to get a translated JavaScript string pushed in with JText::script() in Joomla.
	 */
	Joomla.JText = {
		strings: {},
		'_': function(key, def) {
			return typeof this.strings[key.toUpperCase()] !== 'undefined' ? this.strings[key.toUpperCase()] : def;
		},
		load: function(object) {
			for (var key in object) {
				this.strings[key] = object[key];
			}
			return this;
		}
	};
}
