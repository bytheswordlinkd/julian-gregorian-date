# julian-gregorian-date
MediaWiki extension to display double dates in Julian and Gregorian calendars

## Introduction

This is an extension for MediaWiki. It provides a tag that displays double dates in the Julian and Gregorian calendars, with the day of the week.

## Compatibility

This extension has been tested with MediaWiki 1.35 and 1.39, and PHP 7.3 and 7.4.

The PHP calendar module must be enabled. This is usually enabled by default and is also required by MediaWiki.

The Cite extension needs to be enabled in MediaWiki because this tag can create footnotes.

## Installation

Copy the JulianGregorianDate folder to the 'extensions' directory of your MediaWiki installation.

Add the following code to your LocalSetting.php file:

`wfLoadExtension( 'JulianGregorianDate' );`

## Usage

When the extension is installed and enabled, it provides the tag `<date>`. The tag should always have an attribute called 'when'. The value should be a date entered in the form "YYYY-MM-DD" followed by a space, followed by either:

- "JL" for the Julian calendar
- "GR" for the Gregorian calendar

This indicates which calendar is used by the date that you entered. The tag will display the same date in both calendars, with the day of the week.

For example, if you enter:

`<date when="1642-10-23 JL"/>`

You will get:

Sunday 23 October 1642 <sup>JL</sup> / 2 November 1642 <sup>GR</sup>

### Changing date formats

The optional attribute 'format' can be used to change the format of the output using these values:

- "short" displays the date on one line as above but abbreviates the names of days and months.
- "column" makes it easier to display the date in a table column by splitting it over three lines:
  - day of the week spelt in full
  - Julian date with abbreviated month
  - Gregorian date with abbreviated month

For example, if you enter:

`<date when="1642-10-23 JL" format="short"/>`

You will get:

Sun 23 Oct 1642 <sup>JL</sup> / 2 Nov 1642 <sup>GR</sup>

### Using the tag with contents

If the tag has contents, the contents will be displayed as entered, and the formatted date specified in the attributes will be displayed in a footnote. This is useful for marking up dates in transcripts of historical texts.

For example, if you enter:

`<date when="1642-10-23 JL">when Edgehill fight was</date>`

The footnote after the text will contain:

Sunday 23 October 1642 <sup>JL</sup> / 2 November 1642 <sup>GR</sup>

If you need to mark up a date that is already inside a footnote, you must use this attribute and value:

`display="inline"`

This will put the formatted date in square brackets after the contents instead of creating a footnote. This is needed because you can't have a footnote inside another footnote.

### Using the tag in templates

You can use the tag in the source code of a template and pass the value of a template parameter. In this case you must use the 'tag' parser function, because XML tags will not be parsed correctly.

For example:

`{{#tag: date|| when = {{{Start date|}}} }}`
