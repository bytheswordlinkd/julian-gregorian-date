<?php

class JulianGregorianDate {

	public static function init( &$parser ) {
        
        $parser->setHook( 'date', [ 'JulianGregorianDate', 'formatDate' ] );
		return true;
    }
    
    public static function formatDate( $in, $param = [], $parser = null, $frame = false ) {
        //<date> tag represents a date in transcribed text or free text. Also used for displaying date properties in templates, but entered value should be fed directly into #set with default value of a single space, not empty string. @when is standardized date in form 0000-00-00 JL or GR. This is NOT same as TEI @when, which must be a Gregorian date in ISO format. If day is specified, will display day of the week and double Julian/Gregorian date
        
        //check for params and set to '' if not exist
        if ( !array_key_exists('when', $param) ) {
            $param['when'] = '';
        }
        if ( !array_key_exists('format', $param) ) {
            $param['format'] = '';
        }
        if ( !array_key_exists('display', $param) ) {
            $param['display'] = '';
        }
        if ( !array_key_exists('exact', $param) ) {
            $param['exact'] = '';
        }
            
        if ( $param['when'] != '' ) {
            //trim spaces and then split on space to separate date from calendar
            $calparts = explode(' ', trim( $param['when'] ) );
            //check that there are two parts
            if ( count($calparts) > 1 ) {
                //set calendar
                if ( strtolower( $calparts[1] ) == 'jl' ) {
                    //check for string meaning Julian
                    $cal_int = CAL_JULIAN;
                    $cal_str = 'JL';
                } else {
                    //assume Gregorian if Julian not properly specified
                    $cal_int = CAL_GREGORIAN;
                    $cal_str = 'GR'; 
                }
                    
            }  else {
                //assume Gregorian if Julian not properly specified
                $cal_int = CAL_GREGORIAN;
                $cal_str = 'GR'; 
            }
                
            //split first part on - to get date components
            $dateparts = explode('-', $calparts[0]);
                
            //check length of date array
            if ( count($dateparts) == 1 and strlen($dateparts[0]) > 0 ) {
                //if 1, return year and calendar as entered
                $datestr = htmlspecialchars( $dateparts[0] ) . '&nbsp;' . Html::rawElement('sup', [ ], $cal_str);
            } elseif ( count($dateparts) == 2 ) {
                //if 2, output only month and year, and only one calendar
                //keep year as entered
                //reformat month
                switch ( ltrim( $dateparts[1], '0' ) ) {
                    case '1':
                        $month_str = 'January&nbsp;';
                        break;
                    case '2':
                        $month_str = 'February&nbsp;';
                        break;
                    case '3':
                        $month_str = 'March&nbsp;';
                        break;
                    case '4':
                        $month_str = 'April&nbsp;';
                        break;
                    case '5':
                        $month_str = 'May&nbsp;';
                        break;
                    case '6':
                        $month_str = 'June&nbsp;';
                        break;
                    case '7':
                        $month_str = 'July&nbsp;';
                        break;
                    case '8':
                        $month_str = 'August&nbsp;';
                        break;
                    case '9':
                        $month_str = 'September&nbsp;';
                        break;
                    case '10':
                        $month_str = 'October&nbsp;';
                        break;
                    case '11':
                        $month_str = 'November&nbsp;';
                        break;
                    case '12':
                        $month_str = 'December&nbsp;';
                        break;
                    default:
                        $month_str = '';
                }
                
                $datestr = $month_str . htmlspecialchars( $dateparts[0] ) . '&nbsp;' . Html::rawElement('sup', [ ], $cal_str);
                
            } elseif ( count($dateparts) == 3 ) {
                //if 3, output full date in both calendars
                //get Julian day number (if invalid numbers passed, get weird results but doesn't throw exception)
                $jd = cal_to_jd( $cal_int, (int)$dateparts[1], (int)$dateparts[2], (int)$dateparts[0] );
                    
                //check which calendar has been entered
                //get date components into arrays m-d-y
                if ( $cal_int == CAL_JULIAN ) {
                    //use dateparts to generate Julian date components
                    $jl = array( $dateparts[1], $dateparts[2], $dateparts[0] );
                    //call function to convert to Greg
                    $gr = explode('/', jdtogregorian($jd) );
                } else {
                    //use dateparts to generate Greg date components
                    $gr = array( $dateparts[1], $dateparts[2], $dateparts[0] );
                    //call function to convert to Julian
                    $jl = explode('/', jdtojulian($jd) );
                }
                    
                //CHECK FOR FORMAT PARAM and change datestr if necessary
                if ( $param['format'] == 'short' ) {
                    //short format: all on one line, abbr day and month names
                    $datestr = jddayofweek($jd, 2) . ' ' . ltrim($jl[1], 0) . ' ' . jdmonthname($jd, 2) . ' ' . $jl[2] . '&nbsp;<sup>JL</sup> / ' . ltrim($gr[1], 0) . ' ' . jdmonthname($jd, 0) . ' ' . $gr[2] . '&nbsp;<sup>GR</sup>';
                } elseif ( $param['format'] == 'column' ) {
                    //column format for repeatable sub templates
                    //1st row: full day name
                    //2nd row: JL date with abbr month name
                    //3rd row: GR date with abbr month name
                    $datestr = jddayofweek($jd, 1) . '<br />' . ltrim($jl[1], 0) . '&nbsp;' . jdmonthname($jd, 2) . '&nbsp;' . $jl[2] . '&nbsp;<sup>JL</sup><br />' . ltrim($gr[1], 0) . '&nbsp;' . jdmonthname($jd, 0) . '&nbsp;' . $gr[2] . '&nbsp;<sup>GR</sup>';
                } else {
                    //default for free text
                    //full day and month name
                    $datestr = jddayofweek($jd, 1) . ' ' . ltrim($jl[1], 0) . ' ' . jdmonthname($jd, 3) . ' ' . $jl[2] . '&nbsp;<sup>JL</sup> / ' . ltrim($gr[1], 0) . ' ' . jdmonthname($jd, 1) . ' ' . $gr[2] . '&nbsp;<sup>GR</sup>';
                }
            } else {
                $datestr = '';
            }
                
            //after building formatted date string, need to check $in for tag contents
            if ( $in ) {
                //check for @display
                if ( $param['display'] == 'inline' ) {
                    //if @display="inline" output contents with date string after in square brackets
                    return $parser->recursiveTagParse( $in . ' [' . $datestr . ']', $frame );
                } else {
                    //else output contents with date string as footnote
                    return $parser->recursiveTagParse( $in . '<ref>' . $datestr . '</ref>', $frame );
                }
                    
            } else {
                //else just output date (this also applies if has opening and closing tags with nothing between)
                    
                //check for param for earliest/latest date and add italics if not definite date
                if ( $param['exact'] == 'earliest known date' or $param['exact'] == 'latest known date' ) {
                    //this is not definite date so add italics
                    return '<em>' . $datestr . '</em>';
                        
                } else {
                    //this is exact date so just return date
                    return $datestr;
                }
                    
            }
        } else {
            //if @when is present but empty, just return tag contents
            return $parser->recursiveTagParse( $in, $frame );
        }
        
    }
        
//end of class
}

?>
