<?php

namespace App\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('count',[$this,'countFilter']),//统计
            new TwigFilter('trans_datetime',[$this,'datetime_trans_before']),//转换几分钟前
        );
    }
    public function countFilter($source)
    {
        if(!is_array($source) && !is_object($source)){
            return 0;
        }
        $count = count($source);
        return $count;
    }
    public function datetime_trans_before($datetime)
    {       
        $now_time = date("Y-m-d H:i:s",time()  );  
        $now_time = strtotime($now_time);  
        $show_time = strtotime($datetime);  
        $t = $now_time - $show_time;  
        
        $f=array(  
            '31536000'=>'年',  
            '2592000'=>'个月',  
            '604800'=>'星期',  
            '86400'=>'天',  
            '3600'=>'小时',  
            '60'=>'分钟',  
            '1'=>'秒'  
        );  
        foreach ($f as $k=>$v)    {  
            if (0 !=$c=floor($t/(int)$k)) {  
                return $c.$v.'前';  
            }  
        }
    }


    
}