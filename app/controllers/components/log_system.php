<?php
    /** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 * Dung de luu cac theo tac lien quan den he thong PMS
 * Luu tat ca cac phan chinh sua, mo ta...
 * Luu history 
 */
    class LogSystemComponent extends Object{
        /**
         * Luu cac thao tac ma nguoi dung thuc hien trong he thong
         * $data = array(
         *  'id' => ???,
         *  'company_id' => ???, -> cong ty ma employee dang dang nhap
         *  'model' => ???, -> ten model can luu
         *  'model_id' => ???, -> id cua model can luu
         *  'name' => ???, -> thong tin nguoi luu gom: Name H:i d/m/Y
         *  'description' => ???, -> mo ta thong tin can luu
         *  'employee_id' => ???, -> employee dang thao tac
         *  'update_by_employee' => ??? -> ten employee dang thao tac
         * );
         */
        public function saveLogSystem($datas = array()){
            $Model = ClassRegistry::init('LogSystem');
            $result = '';
            if(!empty($datas)){
                $Model->create();
                if(!empty($datas['id'])){
                    $Model->id = $datas['id'];
                }
                unset($datas['id']);
                if($Model->save($datas)){
                    $result = $Model->read(null);
                    $result['LogSystem']['time'] = date('H:i d-m-Y', $result['LogSystem']['updated']);
                }
            }
            return $result;   
        }
        /**
         * Xoa http:// va https:// trong chuoi nhap vao
         */
        public function cleanHttpString($datas = null){
            $result = '';
            if(!empty($datas)){
                $datas = str_replace('http://', '', $datas);
                $datas = str_replace('https://', '', $datas);
                $result = trim($datas);
            }
            return $result;
        }
    }
?>