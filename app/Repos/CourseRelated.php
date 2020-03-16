<?php

namespace App\Repos;

use App\Models\CourseRelated as CourseRelatedModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CourseRelated extends Repository
{

    /**
     * @param int $courseId
     * @param int $relatedId
     * @return CourseRelatedModel|Model|bool
     */
    public function findCourseRelated($courseId, $relatedId)
    {
        $result = CourseRelatedModel::findFirst([
            'conditions' => 'course_id = :course_id: AND related_id = :related_id:',
            'bind' => ['course_id' => $courseId, 'related_id' => $relatedId],
        ]);

        return $result;
    }

    /**
     * @param array $relatedIds
     * @return ResultsetInterface|Resultset|CourseRelatedModel[]
     */
    public function findByRelatedIds($relatedIds)
    {
        $result = CourseRelatedModel::query()
            ->inWhere('related_id', $relatedIds)
            ->execute();

        return $result;
    }

    /**
     * @param array $courseIds
     * @return ResultsetInterface|Resultset|CourseRelatedModel[]
     */
    public function findByCourseIds($courseIds)
    {
        $result = CourseRelatedModel::query()
            ->inWhere('course_id', $courseIds)
            ->execute();

        return $result;
    }

}
