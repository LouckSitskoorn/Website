<?php
  /*
	 * Created on 16 jun 2010
	 * by Louck Sitskoorn
	*/

  $resultset = array(
    "page"      =>  1,
    "pagecount" =>  1,
    "rowcount"  =>  0,
    "data"      =>  array()
  );

  //json result
  echo json_encode($resultset);
?>