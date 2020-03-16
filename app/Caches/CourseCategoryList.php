<?php

namespace App\Caches;

use App\Repos\Course as CourseRepo;
use Phalcon\Mvc\Model\Resultset;

class CourseCategoryList extends Cache
{

    protected $lifetime = 7 * 86400;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return "course_category_list:{$id}";
    }

    public function getContent($id = null)
    {
        $courseRepo = new CourseRepo();

        /**
         * @var Resultset $categories
         */
        $categories = $courseRepo->findCategories($id);

        if ($categories->count() == 0) {
            return [];
        }

        return $this->handleContent($categories);
    }

    /**
     * @param Resultset $categories
     * @return array
     */
    public function handleContent($categories)
    {
        $result = [];

        foreach ($categories as $category) {
            $result[] = [
                'id' => $category->id,
                'name' => $category->name,
            ];
        }

        return $result;
    }

}
