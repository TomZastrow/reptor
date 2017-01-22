<!--
Licensed to the Apache Software Foundation (ASF) under one
or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information
regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the
"License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing,
software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, either express or implied.  See the License for the
specific language governing permissions and limitations
under the License.
-->

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