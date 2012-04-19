<?php
include("FusionCharts.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>数据统计3D图和导出数据功能</title>
        <script type="text/javascript" src="../../jquery-1.4.2.min.js"></script>
        <!--[if IE 6]>
		<script type="text/javascript" src="DD_belatedPNG_0.0.8a-min.js"></script>
        <script type="text/javascript">
          DD_belatedPNG.fix('img');
        </script>
        <![endif]-->
        <script type="text/javascript" src="FusionCharts.js"></script>
        <script type="text/javascript">
            function exportCharts(exportFormat){
                for ( var chartRef in FusionCharts.items ) {
                    if ( FusionCharts.items[chartRef].exportChart ) {
                        FusionCharts.items[chartRef].exportChart( { "exportFormat" : exportFormat } );
                    } else {
                        alert('请稍后再试......');
                        return false;
                    }
                }
            }
        </script>
    </head>
    <body>
        <center>
            <?php
                FC_SetRenderer( "javascript" );
                echo renderChart("Column3D.swf", "dataresult.xml", "", "chart_zhuzhuang", 710, 260, false, true);
            ?>
           <br />
            <?php
    			FC_SetRenderer( "javascript" );
                echo renderChart("Pie3D.swf", "dataresult.xml", "", "chart_pie", 710, 260, false, true);
            ?>
            <br /> 
            <div><input value="JPG导出" type="button" onClick="JavaScript:exportCharts('JPG')" />&nbsp;&nbsp;&nbsp;<input value="PDF导出" type="button" onClick="JavaScript:exportCharts('PDF')" />&nbsp;&nbsp;&nbsp;<input value="Excle导出" type="button" /></div>
        </center>
    </body>
</html>

