<?php

class UsersController extends \BaseController {

    public function register()
    {
        return View::make('users.register');
    }

    public function registerUser()
    {

        // Create user
        $createUser = static::createUser(Input::get());

        // If there's error
        if (!$createUser['success']) {
            // Return
            return View::make('users.register')->withErrors($createUser['error']);
        }

        // Login credentials
        $credentials = array(
            'mobile'    => $createUser['user']->mobile,
            'password' => Input::get('password'),
        );

        // Authenticate the user
        $user = Sentry::authenticate($credentials, false);

        //echo Sentry::getUser()->id;
        return Redirect::to('home');
    }

    /**
     * Create user
     */
    public static function createUser($user) {
        /**
         * Create
         */
        $create = array(
            'success'=> false,
            'error'=> '',
            'user'=> null
        );

        if (!isset($user['first_name']) || !($user['first_name'] = trim($user['first_name']))) {
            // Set error
            $create['error'] = 'First name is required';
            // Return
            return $create;
        }

        if (!isset($user['last_name']) || !($user['last_name'] = trim($user['last_name']))) {
            // Set error
            $create['error'] = 'Last name is required';
            // Return
            return $create;
        }

        // Check if mobile is valid
        if (Chikka::getNetwork($user['mobile']) === false) {
            // Set error
            $create['error'] = 'Mobile number not supported';
            // Return
            return $create;
        }

        // Set mobile number
        $user['mobile'] = User::shortenMobile($user['mobile']);

        if (!isset($user['password']) || !($user['password'] = trim($user['password']))) {
            // Set error
            $create['error'] = 'Password is required';
            // Return
            return $create;
        }

        try {
            // Create
            $create['user'] = User::createUser($user);
            // If user already in use
        } catch (Cartalyst\Sentry\Users\UserExistsException $e) {
            // Already exists
            $create['error'] = 'Mobile number is already in use';
            // Return
            return $create;
        }

        // Return with success
        $create['success'] = true;
        // Return
        return $create;
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
                'mobile'    => User::shortenMobile(Input::get('mobile')),
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