<?php

class FriendsController extends \BaseController {

	/**
	 * Display a listing of friends
	 *
	 * @return Response
	 */
	public function index()
	{
		$friends = Friend::all();

		return View::make('friends.index', compact('friends'));
	}

	/**
	 * Show the form for creating a new friend
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('friends.create');
	}

	/**
	 * Store a newly created friend in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = User::findByMobile(Input::get('mobile'));

		if(!$user) {
			$user_register = array(
				'first_name' => Input::get('first_name'),
				'last_name' => Input::get('last_name'),
				'name' => Input::get('first_name') . " ". Input::get('last_name'),
				'mobile' => User::shortenMobile(Input::get('mobile')),
				'password' => '1234'
				);

	        User::createUser($user_register);
		}

		$user = User::findByMobile(Input::get('mobile'));

		$data = array(
			'friend_id' => $user->id,
			'user_id' => $this->user->id
			);

		$validator = Validator::make($data, Friend::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Friend::create($data);
		
		return Redirect::to('members/friends');
	}

	/**
	 * Display the specified friend.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$friend = Friend::findOrFail($id);

		return View::make('friends.show', compact('friend'));
	}

	/**
	 * Show the form for editing the specified friend.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$friend = Friend::where('friend_id', "=", $id);

		return View::make('friends.edit', compact('friend'));
	}

	/**
	 * Update the specified friend in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$friend = Friend::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Friend::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$friend->update($data);

		return Redirect::route('friends.index');
	}

	/**
	 * Remove the specified friend from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Friend::where('friend_id', '=', $id)->delete();
		return Redirect::to('members/friends');
	}

}
