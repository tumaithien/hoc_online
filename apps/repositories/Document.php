<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnDocument;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnVideo;
use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnArticle;

class Document extends Component
{
    public static function count() {
        $cache = new CacheRepo("document_count_document");
        $total = $cache->getCache();
        if(!$total) {
            $total = LearnDocument::count([
            ]);
            $total = $cache->setCache($total);
        }
        return $total;
        
    }
    public static function findFirstById($id)
    {
        return LearnDocument::findFirst([
            'document_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }

    public static function findByClassAndSubjectAndChapter($class_id, $subject_id, $chapter_id, $type = "normal")
    {
        return LearnDocument::find([
            'document_class_id = :class_id: AND document_chapter_id = :chapter_id: AND document_type = :type: 
            AND document_subject_id = :subject: AND document_active = "Y" ',
            'bind' => ['class_id' => $class_id, 'subject' => $subject_id, 'chapter_id' => $chapter_id, 'type' => $type],
            'order' => 'document_order ASC'
        ]);
    }
    public static function findByClassAndSubjectAndChapterAndCache($class_id, $subject_id, $chapter_id, $type = "normal")
    {
        $cache = new CacheRepo("document_findByClassAndSubjectAndChapterAndCache_{$class_id}_{$subject_id}_{$chapter_id}_{$type}");
        $data = $cache->getCache();
        if (!$data) {
            $data = LearnDocument::find([
                'document_class_id = :class_id: AND document_chapter_id = :chapter_id: AND document_type = :type: 
                AND document_subject_id = :subject: AND document_active = "Y" ',
                'bind' => ['class_id' => $class_id, 'subject' => $subject_id, 'chapter_id' => $chapter_id, 'type' => $type],
                'order' => 'document_order ASC'
            ])->toArray();
            $cache->setCache($data);
        }
        return $data;
    }
    public static function findByClassAndSubjectAndChapterAndGroup($class_id, $subject_id, $chapter_id, $group_id, $type = "normal")
    {
        return LearnDocument::find([
            'document_class_id = :class_id:
             AND document_chapter_id = :chapter_id: 
             AND document_type = :type: 
             AND (FIND_IN_SET(:group_id:,document_group_ids) OR document_group_ids = "" OR document_group_ids is NULL)
            AND document_subject_id = :subject: AND document_active = "Y" ',
            'bind' => ['class_id' => $class_id, 'subject' => $subject_id, 'chapter_id' => $chapter_id, 'type' => $type, 'group_id' => $group_id],
            'order' => 'document_order ASC'
        ]);
    }
    public static function findHomeDocument()
    {
        $cache = new CacheRepo("document_findHomeDocument",1);
        $data = $cache->getCache();
        if(!$data) {
            $data = LearnDocument::find([
                'limit' => 4,
                'order' => 'RAND()'
            ])->toArray();
            $data = $cache->setCache($data);
        }
        return $data;
    }
}