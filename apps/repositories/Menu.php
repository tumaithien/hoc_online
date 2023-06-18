<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnChapter;
use Learncom\Models\LearnMenu;
use Phalcon\Mvc\User\Component;

class Menu extends Component
{
    public static function findFirstById($id) {
        return LearnMenu::findFirst([
            'menu_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function getCombobox($type) {
        $data = ['tailieu' =>'Tài Liệu',
            'baigiang' => 'Bài Giảng',
            'khoahoc' => 'Khóa Học',
            'start-test' => 'Trắc Nghiệm',
            'blog' => 'Blog',
            'home' => 'Trang chủ',
            'login' => 'Đăng Nhập',
            'default' => 'Giáo Viên',
            'course' => 'Khóa Học Tiêu Biểu',
            'tailieuboiduong' => "Bồi dưỡng HSG",
            'kiem-tra' => "Bài Kiểm Tra"
        ];
        $output = "";
            foreach ($data as $item=> $value) {
                $selected = '';
                if ($item == $type) {
                    $selected = 'selected';
                }
                $output .= "<option " . $selected . " value='" . $item . "'>" . $value . "</option>";
            }
            return $output;
    }

    public static function findMenu(){
        $cache = new CacheRepo("banner_findMenu");
        $data = $cache->getCache();
        if (!$data) {
            $models = LearnMenu::find([
                'menu_active = "Y"',
                'order' => ['menu_order ASC']
            ]);
            $data = $cache->setCache( $models->toArray());
        }
        return $data;
    }

}



