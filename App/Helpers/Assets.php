<?php
namespace Go;
use Go\Url as Url;

class Assets
{

    protected static $templates = array
    (
        'js'  => '<script src="%s" type="text/javascript"></script>',
        'css' => '<link href="%s" rel="stylesheet" type="text/css">'
    );

    protected static function resource( $files, $template )
    {
        $template = self::$templates[ $template ];

        if ( is_array( $files ) ):
            foreach ( $files as $file ):
                if ( !empty( $file ) ):
                    echo sprintf($template, $file) . "\n";
                endif;

            endforeach;
        else:
            if ( !empty( $files ) ):
                echo sprintf( $template, $files ) . "\n";
            endif;
        endif;

    }

    /**
     * Load js scripts.
     *
     * @param  String|Array   $files      paths to file/s
     * @param  boolean|string $cache      if set to true a cache will be created and served
     * @param  boolean        $refresh    if true the cache will be updated
     * @param  string         $cachedMins minutes to hold the cache
     */
    public static function js( $files, $cache = false, $refresh = false, $cachedMins = '1440' )
    {
        $type = 'js';

        if ( $cache == false ):
            static::resource( $files, $type );
        else:
            if ( $refresh == false && file_exists( $path ) && ( filemtime( $path ) > ( time() - 60 * $cachedMins ) ) ) :

                $path = str_replace( SITE_LINK, null, $path );

                static::resource($path, $type);
            else:
                $source = static::collect($files, $type);
                file_put_contents($path, $source);

                $path = str_replace(SITE_LINK, null, $path);
                

                static::resource($path, $type);
            endif;

        endif;
    }

    /**
     * Load css scripts.
     *
     * @param  String|Arra y  $files      paths to file/s
     * @param  boolean|string $cache      if set to true a cache will be created and served
     * @param  boolean        $refresh    if true the cache will be updated
     * @param  string         $cachedMins minutes to hold the cache
     */
    public static function css( $files, $cache = false, $refresh = false, $cachedMins = '1440' )
    {
        $path = ROOT.Url::relativeTemplatePath()."css/$cache.min.css";
        $type = 'css';

        if ( $cache == false) :
            static::resource( $files, $type );
        else:
            if ( $refresh == false && file_exists( $path ) && ( filemtime( $path ) > ( time() - 60 * $cachedMins ) ) ):
                $path = str_replace( ROOT, null, $path );

                static::resource( SITE_LINK.$path, $type );
            else:
                $source = static::collect( $files, $type );
                $source = static::compress( $source );
                file_put_contents( $path, $source );

                $path = str_replace( ROOT, null, $path );

                static::resource( SITE_LINK.$path, $type );
            endif;
        endif;
    }

    private static function collect($files, $type)
    {
        $content = null;
        if (is_array($files)) {
            foreach ($files as $file) {
                if (!empty($file)) {
                    if (strpos(basename($file), '.min.') === false && $type == 'css') {
                        // Compress files that aren't minified
                        $content.= static::compress(file_get_contents($file));

                    } else {
                        $file = str_replace(ROOT, null, $file);
                        $content.= file_get_contents($file);
                    }
                }
            }
        } else {
            if (!empty($files)) {
                if (strpos(basename($files), '.min.') === false && $type == 'css') {
                    // Compress files that aren't minified
                    $content.= static::compress(file_get_contents($files));
                } else {
                    $content.= file_get_contents($files);
                }
            }
        }

        return $content;
    }

    private static function compress($buffer)
    {
        // Remove comments.
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        // Remove tabs, spaces, newlines, etc.
        $buffer = str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '), '', $buffer);
        // Remove other spaces before/after ';'.
        $buffer = preg_replace(array('(( )+{)','({( )+)'), '{', $buffer);
        $buffer = preg_replace(array('(( )+})','(}( )+)','(;( )*})'), '}', $buffer);
        $buffer = preg_replace(array('(;( )+)','(( )+;)'), ';', $buffer);
        return $buffer;
    }
}
