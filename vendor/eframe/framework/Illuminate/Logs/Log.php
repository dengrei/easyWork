<?php
namespace Illuminate\Logs;

class Log
{
	/**
	 *
	|+----------------------------------------
	| 将日志文件转换成图形界面
	|+----------------------------------------
	 */
	public function logToImage()
	{
		$file     = LOG_PATH.'/application.log';
		$pregtimeStr  = '[0-6]{4}\/[0-9]{2}\/[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}'; //时间正则
		
		$filesource = fopen($file,'r');
		$filelenth  = 1024;
		
		$page       = intval($_GET['page']);
		$page       = $page<=1?1:$page;
		$keywords   = trim($_GET['keywords']);
		
		$pagerows   = 20;
		$start      = ($page-1)*$pagerows;
		$end        = $start+$pagerows;
		
		$m = 0;
		$totalRow = 0;
		$currRow  = 0;
		$contents = '';
		while ($row = fgets($filesource,$filelenth)){
			if($keywords){
				if(strpos($row,$keywords) !== false){
					$m++;
					
					if(preg_match('/'.$pregtimeStr.'/',$row)){
						$contents .= preg_replace('/('.$pregtimeStr.')/', '<div style="margin:10px 0;background:red;color:#fff;padding:5px;">${1}</div>', $row);
					}else{
						$contents .= '<div style="padding:10px 15px;background:#eee;">'.$row.'</div>';
					}
					$currRow = $m;
					$total   = $m;
				}
			}else{
				$m++;
				
				if($m >= $start && $m <= $end){
					if(strpos($row,'---') === 0){
						//$contents .= '<div style="height:2px;background:#9c4444;"></div>';
					}else{
						if(preg_match('/'.$pregtimeStr.'/',$row)){
							$contents .= preg_replace('/('.$pregtimeStr.')/', '<div style="margin:10px 0;background:red;color:#fff;padding:5px;">${1}</div>', $row);
						}else{
							$contents .= '<div style="padding:10px 15px;background:#eee;">'.$row.'</div>';
						}
					}
					$currRow = $m;
				}
				$total = $m;
			}
		}
		
		fclose($filesource);

		echo '共<span style="color:red;">'.$total.'</span>行，已读取到<span style="color:red;">'.$currRow.'</span>行，剩余<span style="color:red;">'.($total-$currRow).'</span>行未查看';
		echo $contents;
	}
}