<?php

class ClientsController extends \BaseController {

	/**
	 * Display a listing of clients
	 *
	 * @return Response
	 */
	public function index()
	{
		$clients = Client::all();

		return View::make('clients.index', compact('clients'));
	}

	/**
	 * Show the form for creating a new client
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('clients.create');
	}

	/**
	 * Show the form for creating a new client
	 *
	 * @return Response
	 */
	public function dashboard()
	{
		return View::make('clients.dashboard');
	}


	/**
	 * Show the form for creating a new client
	 *
	 * @return Response
	 */
	public function reservations()
	{
		return View::make('clients.reservations');
	}

	/**
	 * Show the form for creating a new client
	 *
	 * @return Response
	 */
	public function establishment()
	{
		return View::make('clients.establishment');
	}

	/**
	 * Show the form for creating a new client
	 *
	 * @return Response
	 */
	public function credits()
	{
		return View::make('clients.credits');
	}

	/**
	 * Show the form for creating a new client
	 *
	 * @return Response
	 */
	public function profile()
	{
		return View::make('clients.profile');
	}

	/**
	 * Show the form for creating a new member
	 *
	 * @return Response
	 */
	public function friends()
	{
		return View::make('clients.friends');
	}

	/**
	 * Show the form for creating a new member
	 *
	 * @return Response
	 */
	public function subscriptions()
	{
		return View::make('clients.subscriptions');
	}

	/**
	 * Show the form for creating a new member
	 *
	 * @return Response
	 */
	public function subscribers()
	{
		return View::make('clients.subscribers');
	}

	/**
	 * Store a newly created client in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Client::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Client::create($data);

		return Redirect::route('clients.index');
	}

	/**
	 * Display the specified client.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$client = Client::findOrFail($id);

		return View::make('clients.show', compact('client'));
	}

	/**
	 * Show the form for editing the specified client.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$client = Client::find($id);

		return View::make('clients.edit', compact('client'));
	}

	/**
	 * Update the specified client in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$client = Client::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Client::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$client->update($data);

		return Redirect::route('clients.index');
	}

	/**
	 * Remove the specified client from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Client::destroy($id);

		return Redirect::route('clients.index');
	}

}
