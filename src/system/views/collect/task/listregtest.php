<?php
echo "采集地址：".$list_url." 一共采集到".count($td_arr)."条";
?>
	<table class="tb tb2 ">
		<tr class="header">
                <?php
		$thumb_key = array_search("缩略图", $th_arr);
		$link_key = array_search("链接地址", $th_arr);
                foreach($th_arr as $v){
		?>
			<th><?php echo $v; ?></th>
		<?php
		}
		?>
                <th width="100">操作</th>
		</tr>
		<?php
			foreach($td_arr as $tr) {
		?>
		<tr class="hover">
                	<?php
                        foreach($tr as $k=>$td){
			?>
			<td>
			<?php 
			//if(CollectTask::is_image($td)){
			if($thumb_key !== false && $k == $thumb_key){
			?>
			<img src='<?php echo $td;?>' width="200" /><br />
			<?php
			}elseif($link_key !== false && $k == $link_key){
				echo '<a href="'.$td.'" target="_blank">'.$td.'</a>';
			}else{
				echo $td.'<br />';
			}
			?>
                        </td>
			<?php
			}
			?>
		</tr>
		<?php
			}
		?>
	</table>