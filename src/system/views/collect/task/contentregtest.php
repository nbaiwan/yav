
	<table class="tb tb2 ">
		<?php
                foreach($fields as $f){
		?>
                <tr>
			<td colspan="2" class="td27"><?php echo $f['name'];?></td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform" colspan="2">
                        <?php 
			if(is_array($f['value'])){
				foreach($f['value'] as $v){
					if(CollectTask::is_image($v)){
						//echo CollectTask::signName($v).".".CollectTask::getFileType($v);
			?>
			<img src='<?php echo $v;?>' /><br />
			<?php
					}else{
						echo $v.'<br />';
					}
				}
			}else{
				if(CollectTask::is_image($f['value'])){
			?>
			<img src='<?php echo $v;?>' /><br />
			<?php
				}else{
					echo $v.'<br />';	
				}
			}
			?>
                        </td>
		</tr>
                <?php
		}
		?>
	</table>
