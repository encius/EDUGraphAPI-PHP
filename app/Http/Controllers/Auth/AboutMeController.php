<?php
/**
 *  Copyright (c) Microsoft Corporation. All rights reserved. Licensed under the MIT license.
 *  See LICENSE in the project root for license information.
 */

namespace App\Http\Controllers\Auth;
use App\Config\EduConstants;
use App\Config\Roles;
use App\Http\Controllers\Controller;
use App\Services\EducationService;
use App\Services\TokenCacheService;
use App\Services\UserRolesService;
use App\ViewModel\Student;
use App\ViewModel\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;


class AboutMeController extends Controller
{
    private $educationService;
    public function index()
    {
        $displayName = $this->GetDisplayName();
        $role = $this->GetUserRole();
        $favoriteColor = $this->GetFavoriteColor();
        $showSaveMessage = false;
        if (isset($_GET['showSaveMessage'])) {
            $showSaveMessage = true;
        }

        $o365UserId = '';
        $classes = [];
        if (Auth::check()) {
            if (Auth::user())
                $o365UserId = Auth::user()->o365UserId;
        }

        if ($o365UserId) {
            $token = (new TokenCacheService())->getMSGraphToken($o365UserId);
            $classes = (new EducationService($token))->getMySections(false);
        }
        $arrData = array(
            'displayName' => $displayName,
            'role' => $role,
            'favoriteColor' => $favoriteColor,
            'showSaveMessage' => $showSaveMessage,
            'classes' => $classes,
            'o365UserId' => $o365UserId
        );
        return view('auth.aboutme', $arrData);

    }

    public function saveFavoriteColor()
    {
        if (!Auth::user())
            return redirect('/login');
        $color = Input::get('FavoriteColor');
        $user = Auth::user();
        $user->favorite_color = $color;
        $user->save();
        return redirect('/auth/aboutme?showSaveMessage=true');
    }

    private function GetDisplayName()
    {
        $displayName = '';
        if (Auth::user()) {
            $displayName = Auth::user()->email;
            if (Auth::user()->firstName != '')
                $displayName = Auth::user()->firstName . ' ' . Auth::user()->lastName;
        }
        return $displayName;
    }

    private function GetUserRole()
    {
        $role = '';
        $o365userId = null;
        if (Auth::user())
            $o365userId = Auth::user()->o365UserId;


        if ($o365userId)
            $role = (new UserRolesService)->getUserRole($o365userId);
        if($role===Roles::Faculty)
            $role= EduConstants::TeacherObjectType;
        if($role=="")
        {
            $this->educationService = $this->getEduServices();
            $me = $this->educationService->getMe();
            if($me instanceof Student)
            {
                $role="Student";
            }else if($me instanceof Teacher)
            {
                $role="Teacher";
            }
        }
        return $role;
    }

    private function GetFavoriteColor()
    {
        $color = '';
        if (Auth::user()) {
            $color = Auth::user()->favorite_color;
        }
        return $color;
    }

    private function getEduServices()
    {
        $user=Auth::user();
        $token = (new TokenCacheService())->getMSGraphToken($user->o365UserId);
        return new EducationService($token);
    }
}
