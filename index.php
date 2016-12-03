<?php
// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');
$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;
if ($query)
{
  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  require_once('Apache/Solr/Service.php');
  // create a new solr service instance - host, port, and corename
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/csci572/');
  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }
  $params=[];
  if(array_key_exists("pagerank", $_REQUEST)) {
    $params['sort']="pageRankFile desc";
  }
  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
    $results = $solr->search($query, 0, $limit,$params);
  }
  catch (Exception $e)
  {
   // in production you'd probably log or email this error to an admin
   // and then show a special message to the user but for this example
   // we're going to show the full exception
   die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}
?>
<html>
  <head>
  <title>PHP Solr Client Example</title>
  </head>
  <body>
  <form accept-charset="utf-8" method="get">
  <label for="q">Search:</label>
  <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
  <input type="submit"/>
  <input type="checkbox" name="pagerank">Use Page Rank</input>
  </form>
<?php
  $url;
  // display results
  if ($results)
  {
   $total = (int) $results->response->numFound;
   $start = min(1, $total);
   $end = min($limit, $total);
?>
  <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
  <ol>
<?php
  $url_array=[];
  if (($handle = fopen("mergeDataFile.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $url_array[$data[0]]=$data[1];
    }
    fclose($handle);
  }

  // iterate result documents
  foreach ($results->response->docs as $doc)
  {
?>
  <li>
  <table style="border: 1px solid black; text-align: left">
<?php
  // iterate document fields / values
  foreach ($doc as $field => $value)
  {
    if($field=="id") {
      $url=$value;
    }
?>
  <tr>
  <th><?php 
        if($field=="id") {
          echo $field;
        } else if($field=="title") {
          echo $field;
        } else if($field=="author") {
          echo $field;
        } else if($field=="description") {
          echo $field;
        } else if($field=="stream_size") {
          echo $field;
        } else if($field=="conteny_type") {
          echo $field;
        } else if($field=="content_encoding") {
          echo $field;
        } 
  ?></th>
  <td><?php 
        if($field=="id") {
          echo $value;
        } else if($field=="title") {
          echo $value;
        } else if($field=="author") {
          echo $value;
        } else if($field=="description") {
          echo $value;
        } else if($field=="stream_size") {
          echo $value;
        } else if($field=="conteny_type") {
          echo $value;
        } else if($field=="content_encoding") {
          echo $value;
        } 
   ?></td>
  </tr>
 
<?php
  }
?>
 <tr><th>url: </th>
  <td><?php 
        $urls=explode("/",$url);
        $size=sizeof($urls)-1;
        $url_name=$urls[$size];
        echo "<a href='$url_array[$url_name]' target='_blank'>".$url_array[$url_name]."</a>"; 
      ?></td>
  </tr>
 </table>
 </li>
<?php
 }
?>
 </ol>
<?php
  }
?>
 </body>
 </html>