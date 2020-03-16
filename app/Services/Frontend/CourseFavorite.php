<?php

namespace App\Services\Frontend;

use App\Models\CourseFavorite as FavoriteModel;
use App\Models\User as UserModel;
use App\Repos\CourseFavorite as CourseFavoriteRepo;
use App\Validators\UserDailyLimit as UserDailyLimitValidator;

class CourseFavorite extends Service
{

    use CourseTrait;

    public function saveFavorite($id)
    {
        $course = $this->checkCourse($id);

        $user = $this->getLoginUser();

        $validator = new UserDailyLimitValidator();

        $validator->checkFavoriteLimit($user);

        $favoriteRepo = new CourseFavoriteRepo();

        $favorite = $favoriteRepo->findCourseFavorite($course->id, $user->id);

        if (!$favorite) {

            $favorite = new FavoriteModel();

            $favorite->course_id = $course->id;
            $favorite->user_id = $user->id;

            $favorite->create();

            $course->favorite_count += 1;

        } else {

            if ($favorite->deleted == 0) {

                $favorite->deleted = 1;

                $course->favorite_count -= 1;

            } else {

                $favorite->deleted = 0;

                $course->favorite_count += 1;
            }

            $favorite->update();
        }

        $course->update();

        $this->incrUserDailyFavoriteCount($user);
    }

    protected function incrUserDailyFavoriteCount(UserModel $user)
    {
        $this->eventsManager->fire('userDailyCounter:incrFavoriteCount', $this, $user);
    }

}
