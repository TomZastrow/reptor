<?php
header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?> 
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:rs="http://www.openarchives.org/rs/terms/">
    <rs:md capability="resourcelist"
           at="<?php echo date(DATE_ATOM, mktime()); ?>"/>
           <?php
           $scriptPath = realpath(dirname(__FILE__));
           $path = realpath('data');
           $thisUrl = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'];
           try {
               $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
               foreach ($objects as $name => $object) {
                   if (!is_dir($name)) {
                       $name = str_replace($scriptPath, "", $name);
                       echo "<url><loc>$thisUrl$name</loc></url>\n";
                   }
               }
           } catch (Exception $e) {
               echo 'Exception: ', $e->getMessage(), "\n";
           }
           ?>
</urlset>