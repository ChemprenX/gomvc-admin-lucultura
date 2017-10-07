<?php
namespace Go;

class Url
{

    public static function redirect( $url = null, $fullPath = false, $code = 302 )
    {
        // Process the URL according with fullPath.
        $url = ( $fullPath === false ) ? $url : site_url($url);

        // Throw the special Redirect Exception.
        throw new RedirectToException( $url, $code );
    }

    public static function redirectTo( $url = nul )
    {

        header("Location: $url");
    }
    /**
     * Go to the previous url.
     */
    public static function previous()
    {
        // Throw the special Redirect Exception.
        throw new RedirectToException();
    }

    public static function setflash( $setflash = null )
    {

            if( is_array($setflash ) && !empty( $setflash ) ):
                   $message = $setflash['message'];
                   $grap    = $setflash['type'];

                       if( is_array( $setflash['message'] ) ):

                           $message = $setflash['message'];

                       endif;

                   $path = require  'View' . DS . 'Elements'. DS . $grap.'.ctp';

            endif;

        return $path;
    }
    /**
     * Detect the true URI.
     *
     * * @return string parsed URI
     */
    public static function detectUri()
    {
            $requestUri = $_SERVER['REQUEST_URI'];
            $scriptName = $_SERVER['SCRIPT_NAME'];

            $pathName = dirname( $scriptName );

            if ( strpos( $requestUri, $scriptName) === 0 ):
                $requestUri = substr($requestUri, strlen($scriptName));
            elseif ( strpos( $requestUri, $pathName ) === 0 ):
                $requestUri = substr( $requestUri, strlen( $pathName ) );
            endif;

            $uri = parse_url( ltrim( $requestUri, '/' ), PHP_URL_PATH );

            if ( !empty( $uri ) ):
                return str_replace(array('//', '../'), '/', $uri);
            endif;

        // Empty URI of homepage; internally encoded as '/'
        return '/';
    }

    /**
     * Create the absolute address to the assets folder.
     *
     * @param  string|null $module
     * @return string url to assets folder
     */
    public static function resourcePath( $module = null )
    {
        if ( $module !== null ):
            $path = sprintf('modules/%s/', Inflector::tableize( $module ) );
        else:
            $path = '';
        endif;

        return Config::get( 'app.url' ) .$path .'assets/';
    }

    /**
     * Create the absolute address to the template folder.
     *
     * @param  boolean $custom
     * @return string url to template folder
     */
    public static function templatePath()
    {   
        $thatpath = SITEURL.DIR.DIR_APP.DIR.DIR_TEMPLATE.DIR.TEMPLATE.DIR.DIR_VIEW_ASSETS;
        return $thatpath;
    }

    public static function errorView( $file=null ){
        $custom = ERRORVIEW;
        $path = $custom. $file;
        return include_once $path;

    }

    /**
     * Create the relative address to the template folder.
     *
     * @param  boolean $custom
     * @return string path to template folder
     */
    public static function relativeTemplatePath( $custom = TEMPLATE, $folder = '/Asset/')
    {
        return   DS. 'Templates' . DS . $custom .$folder;
    }

    /**
     * Converts plain text urls into HTML links, the second argument will be
     * used as the url label <a href=''>$custom</a>.
     *
     *
     * @param  string $text   data containing the text to read
     * @param  string $custom if provided, this is used for the link label
     *
     * @return string         returns the data with links created around urls
     */
    public static function autoLink( $text, $custom = null )
    {
        $regex   = '@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@';

        if ( $custom === null ):
            $replace = '<a href="http$2://$4">$1$2$3$4</a>';
        else:
            $replace = '<a href="http$2://$4">'.$custom.'</a>';
        endif;

        return preg_replace($regex, $replace, $text);
    }

    /**
     * This function converts a url segment to a safe one, for example:
     * `test name @123` will be converted to `test-name--123`
     * Basicly it works by replacing every character that isn't an letter or an number to an dash sign
     * It will also return all letters in lowercase.
     *
     * @param $slug - The url slug to convert
     *
     * @return mixed|string
     */
    public static function generateSafeSlug( $slug )
    {
        setlocale(LC_ALL, "en_US.utf8");

        $slug = preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $slug));

        $slug = htmlentities($slug, ENT_QUOTES, 'UTF-8');

        $pattern = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $slug = preg_replace($pattern, '$1', $slug);

        $slug = html_entity_decode($slug, ENT_QUOTES, 'UTF-8');

        $pattern = '~[^0-9a-z]+~i';
        $slug = preg_replace($pattern, '-', $slug);

        return strtolower(trim($slug, '-'));
    }

    /**
     * Retrieve all url parts based on a / seperator.
     *
     * @return array of segments
     */
    public static function segments()
    {
        return explode('/', $_SERVER['REQUEST_URI']);
    }

    /**
     * Retrieve an item in an array.
     *
     * @param  array $segments array
     * @param  int $id array index
     *
     * @return string - returns array index
     */
    public static function getSegment($segments, $id)
    {
        if ( array_key_exists( $id, $segments ) ):
            return $segments[ $id ];

        endif;
    }

    /**
     * Retrieve the last item in an array.
     *
     * @param  array $segments
     * @return string - last array segment
     */
    public static function lastSegment( $segments )
    {
        return end( $segments );
    }

    /**
     * Retrieve the first item in an array
     *
     * @param  array segments
     * @return int - returns first first array index
     */
    public static function firstSegment( $segments )
    {
        return $segments[0];
    }
}
