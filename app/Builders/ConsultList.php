<?php

namespace App\Builders;

use App\Repos\Course as CourseRepo;
use App\Repos\User as UserRepo;

class ConsultList extends Builder
{

    public function handleCourses($consults)
    {
        $courses = $this->getCourses($consults);

        foreach ($consults as $key => $consult) {
            $consults[$key]['course'] = $courses[$consult['course_id']] ?? [];
        }

        return $consults;
    }

    public function handleUsers($consults)
    {
        $users = $this->getUsers($consults);

        foreach ($consults as $key => $consult) {
            $consults[$key]['user'] = $users[$consult['user_id']] ?? [];
        }

        return $consults;
    }

    public function getCourses($consults)
    {
        $ids = kg_array_column($consults, 'course_id');

        $courseRepo = new CourseRepo();

        $courses = $courseRepo->findByIds($ids, ['id', 'title']);

        $result = [];

        foreach ($courses->toArray() as $course) {
            $result[$course['id']] = $course;
        }

        return $result;
    }

    public function getUsers($consults)
    {
        $ids = kg_array_column($consults, 'user_id');

        $userRepo = new UserRepo();

        $users = $userRepo->findByIds($ids, ['id', 'name', 'avatar']);

        $imgBaseUrl = kg_img_base_url();

        $result = [];

        foreach ($users->toArray() as $user) {
            $user['avatar'] = $imgBaseUrl . $user['avatar'];
            $result[$user['id']] = $user;
        }

        return $result;
    }

}
