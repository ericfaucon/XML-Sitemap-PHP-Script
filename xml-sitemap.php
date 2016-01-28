<?php
/************************************************************************/
/*              		 CONFIGURATION          		*/
/************************************************************************/
/*
 * The directory to check.
 * Make sure the DIR ends ups in the Sitemap Dir URL below, otherwise the links to files will be broken!
 */
$path = '.';
// The URL corresponding to the directory above
$url = "http://www.my-website.com";
// Files type to be included in the sitemap
$includedExt = array('js', 'html', 'php');
// Files type to be excluded from the sitemap
$excludedFiles = array();
// The Change Frequency for files, should probably not be 'never', unless you know for sure you'll never change them again.
$chfreq = 'never';
// The Priority Frequency for files. There's no way to differentiate so it might just as well be 1.
$prio = 1;
// The XSL file used for styling the sitemap output, make sure this path is relative to the root of the site.
$xsl = 'xml-sitemap.xsl';


// Send the correct header so browsers display properly, with or without XSL.
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
if (isset($xsl) && !empty($xsl)) {
	echo '<?xml-stylesheet type="text/xsl" href="'.$url.'/'.$xsl.'"?>'."\n";
}
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

/* 
 * Pre-load
 */
$currFile = substr(strrchr(__FILE__ , '\\'), 1);
$excludedFiles = array_merge($excludedFiles, array('.', '..', $currFile));    
$realPath = realpath($path);
$dirList = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($realPath), RecursiveIteratorIterator::SELF_FIRST);

foreach($dirList as $file){
    if (!$file->isDir()) {
        if (in_array($file->getExtension(), $includedExt)) {
            if (!in_array($file->getFilename(), $excludedFiles)) {
                //var_dump($file);
                $relativePath = str_replace($realPath, "", $file->getPathName());
                $modifiedDate = date( 'c', $file->getMTime());
                $urlFile = str_replace($file->getFilename(), rawurlencode($file->getFilename()), $relativePath);
                $urlFile = $url . str_replace('\\', '/', $urlFile);
                $xmlCode = "            
                    <url>
                        <loc>$urlFile</loc>
                        <lastmod>$modifiedDate</lastmod>
                        <changefreq>$chfreq</changefreq>
                        <priority>$prio</priority>
                    </url>
                ";
                printf($xmlCode);
            }
        }
    }
}
echo "</urlset>";
?>
