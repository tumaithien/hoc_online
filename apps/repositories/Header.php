<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnChapter;
use Learncom\Models\LearnGroup;
use Phalcon\Mvc\User\Component;

class Header extends Component
{
    public function getHtmlHeader($arrClass,$arrSubject,$type,$menuExcluded) {
        $result_return = "";
        $url = $type.'?classId=';
        foreach ($arrClass as $class) {
            $temp = 0;
            $result = "";
            $result .= ' <div class="dropdown-nav">
                            <span class="nav-subitem">';
            $result .= $class->getClassName();
            $result .= "</span>";
            $result .= '<div class="dropdown-content-sub">';
            foreach ($arrSubject as $subject) {
                if (!in_array($class->getClassId() . '-' . $subject->getSubjectId(), $menuExcluded)) {
                    $temp ++;
                    if (count(ClassSubject::getSubjectByParentId($subject->getSubjectId())) > 0 && $type ==='tailieu') {
                        $result .= ' <div class="dropdown-nav">
                                 <span class="nav-subitem">';
                        $result .= $subject->getSubjectName();
                        $result .= '      </span>
                                    <div class="dropdown-content-sub">';
                        $arrSubjectParent = ClassSubject::getSubjectByParentId($subject->getSubjectId());
                        foreach ($arrSubjectParent as $subjectParent) {
                            $result .= '<span class="nav-subitem"><a href="'.$this->url->get($url).$class->getClassId().'&subjectId='.$subjectParent->getSubjectId().'">';
                            $result .= $subjectParent->getSubjectName();
                            $result .= '</a></span>';
                        }
                        $result .= '</div></div>';
                    } else {
                        $result .= '<span class="nav-subitem"><a href="'.$this->url->get($url).$class->getClassId().'&subjectId='.$subject->getSubjectId().'">';
                        $result .= $subject->getSubjectName();
                        $result .= '</a></span>';
                    }
                }
            }
            $result .= "</div> </div>";
            if ($temp > 0) {
                $result_return .= $result;
            }
        }
        return $result_return;
    }


}



