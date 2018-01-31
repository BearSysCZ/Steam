# PHP Steam libraries

This package combines several useful PHP libraries for working with Steam IDs, Steam Web API and Steam oAuth.

Can be used standalone or as Nette framework extension.

Package currently includes these libraries:
- [`Ehesp/Steam-Login`](https://github.com/Ehesp/Steam-Login) - used for Nette login component
- [`xPaw/SteamID`](https://github.com/xPaw/SteamID.php) - used for conversions of Steam IDs
- [`zyberspace/php-steam-web-api-client`](https://github.com/zyberspace/php-steam-web-api-client) - used for querying Steam Web API

## Installation

```
composer require bearsys/steam
```

## Usage

Note that Valve limits requests to this API (if you don't have publisher's key). There should be daily limit of 100,000 requests. That is 69 requests per minute. But you could probably make all 100,000 requests at once, although it is not recommended.

### Standalone

Create instance of `BearSys\Steam\SteamWrapper`, provide you Steam Web API user key as constructor parameter.

You can then work with this wrapper as you wish.

#### Example:
```php
$apiKey = 'KEY_GOES_HERE'; // Your API key, matches regex ^[0-9A-F]{32}$
$wrapper = new BearSys\Steam\SteamWrapper($apiKey);

$user = $wrapper->getUser('hitzor'); // you could use any form of Steam ID or vanity URL
$user->getInfo(); // returns info about selected user

$game = $wrapper->getGame(730); // 730 is CS:GO's AppID
$game->getActivePlayersCount(); // returns how many players are currently playing
```

### Nette framework

Register extension in your config and set Steam Web API key.
```neon
extensions:
    steam: BearSys\Steam\Bridges\NetteDI\SteamExtension

steam:
    apiKey: # your Steam Web API key goes here
```
You can request SteamWrapper after that in every class that is created using Nette DI.

#### Login component
Extension will register login factory. Inject it into your presenter (or any other component) and use it's setup method to set callbacks.
```php
/** @var BearSys\Steam\Bridges\NetteApplication\SteamLoginFactory @inject */
public $loginFactory;
// you could use any suitable injection method, this is just the example

public function createComponentSteamLogin()
{
	$successCallback = function (string $steamId, bool $registered) {
		if ($registered)
			$this->redirect('this');
		else
			$this->redirect('User:signUpSteam', $steamId);
	};
	
	$failureCallback = function (\Exception $e) {
		// log $e
	};
	
	return $this->loginFactory->setup(
		function (SteamLogin $login) use ($successCallback, $failureCallback) {
			$this->steamAuthenticator->login($login, $successCallback, $failureCallback);
		}
	);
}
```

Then you need to handle login inside your own authenticator. You could also do that inside your callback and then just call `Nette\Security\User::login()` method with `Nette\Security\IIdentity` instance, but we recommend to don't do that.
```php
use Ehesp\SteamLogin\SteamLogin;

class SteamAuthenticator extends AbstractAuthenticator
{
	public function login(SteamLogin $login, callable $success, callable $failure)
	{
		try {
			$steamId = $login->validate(); // if this failed, it will throw an exception
			
			/*
			 * Your custom logic goes here.
			 * 
			 * You will probably want to check, if Steam ID is in database.
			 * If it is, then just login associated user.
			 * If it isn't, then create user and redirect him to sign-up page
			 * with pre-filled username from Steam (obtainable using SteamWrapper).
			 */
			 
			$success($steamId, $registered);
		} catch (\Exception $e) {
			// Login attempt failed
			$failure($e);
		}
	}
}
```
At last, create link to component handle and style it however you want.
```latte
<a href="{link steamLogin-steamLogin!}">Steam login</a>
```

## Future plans

- Tests (ASAP)
- Integrate `koraktor/steam-condenser-php`
- Add support for Symfony, Laravel and other frameworks (pull-requests are welcomed)
- Consider supporting Steamworks publisher's API endpoints