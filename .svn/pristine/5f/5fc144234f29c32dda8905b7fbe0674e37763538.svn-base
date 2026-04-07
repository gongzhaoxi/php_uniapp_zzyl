<?php
namespace app\common\util;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
/**
 * Excel通用类
 */
class Excel
{
    /**
     * 从数据库导出数据到表格
     * 
     *    使用方式
     *    use app\common\util\Excel;
     *    $title = '管理信息表';
     *    $column = ['日期','账号','状态'];
     *    $setWidh = ['30','30','30'];
     *    $list = AdminAdmin::select()->toArray();
     *    $keys = ['create_time','username','status'];
     *    $filename = "管理表";
     *    Excel::go($title, $column, $setWidh, $list, $keys,$filename);
     * 
     * @param sring $title 首行标题内容
     * @param array $column        第二行列头标题
     * @param array $setWidth      第二行列头宽度
     * @param array $list          从数据库获取表格内容
     * @param array $keys          要获取的内容键名
     * @param string $filename     导出的文件名
     */
    public static function go(string $title, array $column, array $setWidth, array $list, array $keys, string $filename='', array $image_fields = [],$file_path='')
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $count = count($column);
        // 合并首行单元格
        //$worksheet->mergeCells(chr(65).'1:'.chr($count+64).'1');
        $styleArray = [

			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical'   => Alignment::VERTICAL_CENTER,
			]
        ];
        // 设置首行单元格内容
        $worksheet->setTitle($title);
        //$worksheet->setCellValueByColumnAndRow(1, 1, $title);
        // 设置单元格样式
        //$worksheet->getStyle(chr(65).'1')->applyFromArray($styleArray)->getFont()->setSize(18);
        //$worksheet->getStyle(chr(65).'1:'.chr($count+64).'1')->applyFromArray($styleArray)->getFont()->setSize(12);
        // 设置列头内容
        foreach ($column as $key => $value) $worksheet->setCellValueByColumnAndRow($key+1, 1, $value);
        // 设置列头格式
        //foreach ($setWidth as $k => $v) $worksheet->getColumnDimension(chr($k+65))->setWidth(intval($v));
		$colum = 'A';
        foreach ($setWidth as $k => $v) {
			$worksheet->getColumnDimension($colum)->setWidth(intval($v));
			if($k == count($setWidth) - 1){
				$worksheet->getStyle('A1:'.($colum).'1')->applyFromArray($styleArray)->getFont()->setSize(12);	
			}
			$colum++;
		}
		$drawing = [];
		$excel_letter = array ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD' );
		
		$image_fields_count = [];
        // 从数据库获取表格内容
        $len = count($list);
        $j = 0;
        for ($i=0; $i < $len; $i++){
            $j = $i + 2; //从表格第3行开始
            foreach ($keys as $kk => $vv){				

				if(in_array($vv,$image_fields)){				
					if(!empty($list[$i][$vv])){					
						$images = is_array($list[$i][$vv])?$list[$i][$vv]:explode(',',$list[$i][$vv]);
						$count 	= count($images);
						if($count > 1 || (empty($image_fields_count[$vv]) || $image_fields_count[$vv] < $count) ){
							$image_fields_count[$vv] = $count;
						}
						
						foreach($images as $images_key=>$images_value){
							
							if(!empty($images_value) &&  file_exists('.'.$images_value)){
								$drawing[$i][$vv][$images_key] = new Drawing();
								$drawing[$i][$vv][$images_key]->setPath('.'.$images_value);
								$drawing[$i][$vv][$images_key]->setWidth(80);
								$drawing[$i][$vv][$images_key]->setHeight(80);
								$drawing[$i][$vv][$images_key]->setCoordinates($excel_letter[$kk].$j);
								$drawing[$i][$vv][$images_key]->setOffsetX(10+$images_key*80);
								$drawing[$i][$vv][$images_key]->setOffsetY(10);
								$drawing[$i][$vv][$images_key]->setWidthAndHeight(80, 80); // 设置图片宽度和高度
								$drawing[$i][$vv][$images_key]->setResizeProportional(true); // 设置是否保持图片宽高比

								$spreadsheet->getActiveSheet()->getRowDimension($j)->setRowHeight(100);
								$drawing[$i][$vv][$images_key]->setWorksheet($spreadsheet->getActiveSheet());	
							
							}
						}			
					}
				}else {
					$worksheet->setCellValueExplicitByColumnAndRow($kk+1, $j, !isset($list[$i][$vv])?'':$list[$i][$vv],DataType::TYPE_STRING);
					$worksheet->getStyle($excel_letter[$kk].$j)->applyFromArray($styleArray);
				}

            }
        }
		
		$colum = 'A';
		foreach ($keys as $kk => $vv){		
			if(!empty($image_fields_count[$vv])){
				$worksheet->getColumnDimension($colum)->setWidth(intval($image_fields_count[$vv])*15);
			}
			$colum++;
		}		
		
		
        $total_jzInfo = $len + 1;
        $styleArrayBody = [

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
		$writer 	= IOFactory::createWriter($spreadsheet, 'Xlsx');
        // 添加所有边框/居中
        //$worksheet->getStyle(chr(65).'1:'.chr($count+64).$total_jzInfo)->applyFromArray($styleArrayBody);
		if($file_path == ''){
			$filename 	= $filename.date('_YmdHis');
			header('Content-Type: application/vnd.ms-excel');
			header("Content-Disposition:attachment;filename={$filename}.xlsx");
			header('Cache-Control: max-age=0');//禁止缓存
			
			$writer->save('php://output');
		}else{
			$writer->save($file_path.$filename.'.xlsx'); 
		}
    }
}
