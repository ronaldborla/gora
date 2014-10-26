<?php

class UsersController extends \BaseController {

    public function register()
    {
        return View::make('users.register');
    }

    public function registerUser()
    {
        try
        {
            $user = Sentry::createUser(array(
                'first_name'=> Input::get('first_name'),
                'last_name' => Input::get('last_name'),
                'mobile'    => Input::get('mobile'),
                'password'  => Input::get('password'),
                'activated' => true,
                ));

            $adminGroup = Sentry::findGroupById(3);

            $user->addGroup($adminGroup);
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            return View::make('users.register')->withErrors('Login field is required.');
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            return View::make('users.register')->withErrors('Password field is required.');
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            return View::make('users.register')->withErrors('User with this login already exists.');
        }
        catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
        {
            return View::make('users.register')->withErrors('Group was not found.');
        }


        // Login credentials
        $credentials = array(
            'mobile'    => Input::get('mobile'),
            'password' => Input::get('password'),
            );

        // Authenticate the user
        $user = Sentry::authenticate($credentials, false);

        //echo Sentry::getUser()->id;
        return Redirect::to('home');
    }

    /**
     * Show the form for creating a new user
     *
     * @return Response
     */
    public function login()
    {
        return View::make('users.login');
    }

    /**
     * Show the form for creating a new user
     *
     * @return Response
     */
    public function authenticate()
    {
        try
        {
        // Login credentials
            $credentials = array(
                'mobile'    => Input::get('mobile'),
                'password' => Input::get('password'),
                );

        // Authenticate the user
            $user = Sentry::authenticate($credentials, false);
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            return View::make('users.login')->withErrors('Login field is required.');
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            return View::make('users.login')->withErrors('Password field is required.');
        }
        catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
        {
            return View::make('users.login')->withErrors('Wrong password, try again.');
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            return View::make('users.login')->withErrors('User was not found.');
        }
        catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            return View::make('users.login')->withErrors('User is not activated.');
        }

        // The following is only required if the throttling is enabled
        catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
        {
            return View::make('users.login')->withErrors('User is suspended.');
        }
        catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
        {
            return View::make('users.login')->withErrors('User is suspended.');
        }

        //echo Sentry::getUser()->id;
        return Redirect::to('home');

    }

    /**
     * Display a listing of users
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();

        return View::make('users.index', compact('users'));
    }


    /**
     * Show the form for creating a new user
     *
     * @return Response
     */
    public function search()
    {
        return View::make('users.search');
    }

    /**
     * Show the form for creating a new user
     *
     * @return Response
     */
    public function announcements()
    {
        return View::make('users.announcements');
    }

    /**
     * Show the form for creating a new user
     *
     * @return Response
     */
    public function create()
    {
        return View::make('users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @return Response
     */
    public function store()
    {
        $validator = Validator::make($data = Input::all(), User::$rules);

        if ($validator->fails())
        {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        User::create($data);

        return Redirect::route('users.index');
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return View::make('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        return View::make('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($data = Input::all(), User::$rules);

        if ($validator->fails())
        {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $user->update($data);

        return Redirect::route('users.index');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        User::destroy($id);

        return Redirect::route('users.index');
    }

}