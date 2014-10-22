<?php

/**
 * Require Ganon
 */
require_once(app_path().'/support/Ganon.php');

class ImportController extends BaseController {

  /**
   * TripAdvisor
   */
  function tripadvisor() {

    // Do next
    $next = Input::get('next') !== null;
    $skip = Input::get('skip') !== null;

    // Get url
    $url = $this->getNext();
    // Get info
    $info = $url ? $this->extractTripAdvisor($url) : array();

    // Duped
    $duped = false;

    if ($info) {
      // Check if duped
      $duped = $this->duped($info);
    }
    // Imported
    $imported = false;
    $skipped = false;

    // If there's url
    if ($info) {

      if ($next) {
        // Do import
        if (!$duped) {
          // Do import
          $establishment = $this->importEstablishment($info);
          // Set imported
          $imported = $establishment->id ? $establishment->name : '';
        }
      }
      if ($next || $skip) {
        // Skipped
        $skipped = $skip ? $info['establishment']['name'] : false;
        // Remove
        $this->removeFromList($url);
        // Get url
        $url = $this->getNext();
        // Get info
        $info = $url ? $this->extractTripAdvisor($url) : array();

        $duped = $info ? $this->duped($info) : false;
      }
    }

?>
<html>
<head>
<title>Import Restaurants</title>
</head>
<body>
<?php

    echo Form::open(array('action'=> 'ImportController@tripadvisor', 'method'=> 'get'));

    $props = array('type'=> 'submit', 'name'=> 'next');
    if ($duped) $props['disabled'] = 'disabled';

    echo Form::button('Import and Next', $props);
    echo Form::button('Skip', array('type'=> 'submit', 'name'=> 'skip'));

    echo Form::close();

    if ($imported !== false) {
      echo '<div>',($imported ? ('Successfully imported ' . e($imported)) : ('Failed to import ' . e($imported))),'</div>';
    }
    if ($skipped) {
      echo '<div>Skipped ',e($skipped),'</div>';
    }
    if ($duped) {
      echo '<div style="color: #880000">This restaurant already exists in the database</div>';
    }

    // If there's info
    if ($info) {
      echo '<a href="',$url,'" target="_blank">View restaurant</a>';
      echo '<pre>',print_r($info,true),'</pre>';
    }
?>
</body>
</html>
<?php
  }

  /**
   * Add list
   */
  function elist() {

    //$this->removeFromList('http://www.tripadvisor.com.ph/Restaurant_Review-g298445-d2525997-Reviews-Chaya-Baguio_Benguet_Province_Cordillera_Region_Luzon.html');

    if (Input::hasFile('list')) {
      // List
      $list = array();
      // Load file
      $rawList = explode("\n", @file_get_contents(Input::file('list')->getPathname()));
      // Loop
      if ($rawList) {
        foreach ($rawList as $rawItem) {
          if (!($rawItem = trim($rawItem))) continue;
          // Check if valid
          if (preg_match('/(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/', $rawItem)) {
            // add 
            $list[] = $rawItem;
          }
        }
      }
      // Add to list
      $this->addToList($list);
    }

?>
<html>
<head>
<title>Import Restaurants</title>
</head>
<body>
    <?php

    echo Form::open(array('action'=> 'ImportController@elist', 'files'=> true));

    echo Form::file('list');
    echo Form::button('Upload', array('type'=> 'submit'));

    echo Form::close();

    if (is_file($file = public_path() . '/list.txt')) {
      // Print
      echo '<a href="/list.txt" target="_blank">View list</a>';
    }

    ?>
</body>
</html>
<?php
  }

  // Check if duped
  function duped($info) {
    return Establishment::where('name', '=', $info['establishment']['name'])
                        ->where('address', '=', $info['establishment']['address'])
                        ->limit(1)
                        ->first() ? true : false;
  }

  /**
   * Get list
   */
  function getList() {
    // Get list
    $list = explode("\n", @file_get_contents(public_path().'/list.txt'));
    // If first item is empty
    if (!$list[0]) $list = array();
    // Return
    return $list;
  }

  /**
   * Get next
   */
  function getNext() {
    $list = $this->getList();
    return (isset($list[0]) && $list[0]) ? $list[0] : null;
  }

  /**
   * Add to list
   */
  function addToList($list) {

    // Oldlist
    $oldList = $this->getList();

    if (is_array($list) && $list) {
      foreach ($list as $item) {
        // Cleanup
        if (!($item = trim($item))) continue;
        // Make sure it still doesnt exist in list
        if (!in_array($item, $oldList)) {
          // Add to list
          $oldList[] = $item;
        }
      }
    }

    // Save oldlist
    @file_put_contents(public_path().'/list.txt', implode("\n", $oldList));
  }

  /**
   * Remove from list
   */
  function removeFromList($item) {
    // Get list
    $list = $this->getList();

    if (($key = array_search($item, $list)) !== false) {
      // Unset
      unset($list[$key]);
      // Save
      @file_put_contents(public_path().'/list.txt', implode("\n", $list));
    }
  }

  /**
   * Import restaurant
   */
  function importEstablishment($info) {
    // Create new establishment
    $establishment = new Establishment($info['establishment']);
    // Save
    $establishment->save();

    // Find restaurant category
    $restaurant = Category::where('name', '=', 'restaurant')->limit(1)->first();
    // Get id
    $restaurantId = ($restaurant && $restaurant->id) ? $restaurant->id : 0;

    // If there are categories
    if (isset($info['categories']) && $info['categories']) {
      // Set categories
      $categories = array();
      // Loop through each
      foreach ($info['categories'] as $category) {
        // Append to categories
        $categories[] = Category::createNew(array(
          // Set category name
          'name'=> Str::lower($category)
        ), $restaurantId);
      }
      // Update categories
      $establishment->updateCategories($categories);
    }

    // If there are contacts
    if (isset($info['contacts']) && $info['contacts']) {
      // Contact order
      $contactOrder = 0;
      // Loop through each
      foreach ($info['contacts'] as $type=> $contact) {
        // Loop through each
        foreach ($contact as $value) {

          // Create contact
          $newContact = new Contact(array(
            // Set type
            'type'=> array_search($type, Contact::getTypes()),
            // Set value
            'value'=> $value,
            // Set order
            'order'=> $contactOrder,
            // Primary
            'primary'=> (($contactOrder == 0) ? 1 : 0),
            // Set establishment
            'establishment_id'=> $establishment->id
          ));
          // Save
          $newContact->save();

          // Increment order
          $contactOrder++;
        }
      }
    }

    // Return establishment
    return $establishment;
  }

  /**
   * Extract tripadvisor
   */
  function extractTripAdvisor($url) {
    // Get html
    $html = trim(@static::get($url));
    // If there's no html
    if (!$html) return false;
    // Get latLng
    $latLng = explode(',', static::between($html, '&center=', '&zoom'));
    // Price range
    $priceMin = 0;
    $priceMax = 0;
    // If there's price range
    if (strpos($html, '<b>Price range:</b>') !== false) {
      // Get price range
      $priceRange = static::between($html, '<b>Price range:</b>', '</div>');
      // If there's price range
      if ($priceRange) {
        // Get what's in span
        $inSpan = static::between($priceRange, '<span>', '</span>');
        // If there's any
        if ($inSpan) {
          // Split by -
          $arrRange = explode('-', $inSpan);
          // Set min
          $priceMin = intval(static::removeNonNumerics($arrRange[0]));
          // If there's second
          if (isset($arrRange[1])) {
            // Get max
            $priceMax = intval(static::removeNonNumerics($arrRange[1]));
          }
        }
      }
    }
    // Set price multiplier
    $multiplier = (App::environment() == 'live') ? 35 : 1;
    // Get address
    $addressHTML = '<div>'.static::between($html, '<address>', '</address>').'</div>';
    // Dom
    $addressDOM = str_get_dom($addressHTML);
    // If there's HTML, get individual contents
    $establishment = array(
      // Get name
      'name'=> static::between($html, '<div class="warLocName">', '</div>'),
      // Get address
      'address'=> trim(str_replace(' |', ',', $addressDOM->getPlainTextUTF8())),
      // Set latLng
      'lat'=> isset($latLng[1]) ? $latLng[0] : 0,
      'lng'=> isset($latLng[1]) ? $latLng[1] : 0,
      // Set price range
      'price_min'=> round($priceMin * $multiplier, -2),
      'price_max'=> round($priceMax * $multiplier, -2)
    );
    // Set categories
    $categories = array();
    // Get categories
    if (strpos($html, '<b>Cuisines:</b>') !== false) {
      // Get cuisines
      $cuisines = '<div>'.static::between($html, '<b>Cuisines:</b>', '</div>').'</div>';
      // Get dom
      $dom = str_get_dom($cuisines);
      // Get plain text
      $plainText = $dom->getPlainTextUTF8();
      // If there's any
      if ($plainText) {
        // Split by comma
        $arrCategories = explode(',', $plainText);
        // Loop
        foreach ($arrCategories as $category) {
          // Trim
          $category = trim($category);
          // If there's any
          if ($category) {
            // Add to category
            $categories[] = $category;
          }
        }
        // Unique
        $categories = array_unique($categories);
      }
    }
    // Get contact
    $contacts = array();
    // Get phone
    if (strpos($html, '<div class="fl phoneNumber">') !== false) {
      // Phone
      $phoneNumbers = static::between($html, '<div class="fl phoneNumber">', '</div>');
      // Mobiles
      $mobiles = array();
      // Telephones
      $telephones = array();
      // Split by /
      $bySlash = explode('/', $phoneNumbers);
      // Loop through each
      foreach ($bySlash as $singleSlash) {
        // By ,
        $byComma = explode(',', $singleSlash);
        // Loop through each
        foreach ($byComma as $singleComma) {
          // By |
          $byPipe = explode('|', $singleComma);
          // Loop
          foreach ($byPipe as $singlePipe) {
            // By ;
            $bySemiColon = explode(';', $singlePipe);
            // Loop through final
            foreach ($bySemiColon as $singleSemiColon) {
              // Get phone
              $phoneNumber = static::fixPhoneNumber($singleSemiColon);
              // If mobile
              if (static::isMobile($phoneNumber)) {
                // Add to mobile
                $mobiles[] = $phoneNumber;
              } else {
                // Add to telephone
                $telephones[] = $phoneNumber;
              }
            }
          }
        }
      }
      // If there's mobiles
      if ($mobiles) $contacts['mobile'] = $mobiles;
      // If there's telephones
      if ($telephones) $contacts['telephone'] = $telephones;
    }
    // Email left
    $leftEmail = '\'ta.locationDetail.checkEmailAction\',event,this,\'';
    // Get email
    if (strpos($html, $leftEmail) !== false) {
      // Email
      $email = trim(static::between($html, $leftEmail, '\''));
      // If there's any
      if ($email) {
        // Add to contacts
        $contacts['email'] = array($email);
      }
    }
    // Return
    return array(
      'establishment'=> $establishment,
      'categories'=> $categories,
      'contacts'=> $contacts
    );
  }


  /**
   * Get string between two strings
   */
  static function between($str, $left, $right) {
    // Quote
    $quote = '#';
    // Define regex
    $regex = $quote . preg_quote($left, $quote) . '(.*?)' . preg_quote($right, $quote) . $quote . 's';
    // Do preg match
    @preg_match($regex, $str, $matches);
    // Return string
    return isset($matches[1]) ? $matches[1] : NULL;
  }

  static function allBetween($str, $left, $right) {
    // Quote
    $quote = '#';
    // Define regex
    $regex = $quote . preg_quote($left, $quote) . '(.*?)' . preg_quote($right, $quote) . $quote . 's';
    // Do preg match
    @preg_match_all($regex, $str, $matches);
    
    return isset($matches[0])?$matches[0]:NULL;
  }

  /**
   * Remove non numerics
   */
  static function removeNonNumerics($str) {
    // Set new str
    $newStr = '';
    // Get length of string
    $len = strlen($str);

    for ($i = 0; $i < $len; $i++) {
      // Get ord
      $ord = ord($str[$i]);
      // Check if decimal or numeric
      if ($ord == 46 || ($ord >= 48 && $ord <= 57)) {
        // Append
        $newStr .= $str[$i];
      }
    }
    // Return new str
    return $newStr;
  }

  static function get($url) {
    // Initialize curl
    $curl = curl_init();
    // Set options
    curl_setopt($curl, CURLOPT_URL, $url);
    // Encode fields
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    // Return response
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // SSL
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // Set headers
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Encoding: ',
      'Accept-Language: en-US,en;q=0.5',
      'Connection: keep-alive',
      'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0'
    ));
    // Execute
    $response = curl_exec($curl);
    // Close
    curl_close($curl);
    // Return response
    return $response;
  }

  /**
   * Fix phone number
   */
  static function fixPhoneNumber($phone) {
    // Cleanup phone
    $phone = str_replace('.', '', static::removeNonNumerics($phone));
    // If first digit is 0, convert to 63
    if ($phone[0] == '0') {
      // Remove 0, and prepend 63
      $phone = '63' . substr($phone, 1);
    }
    // If first 2 digits is 63
    if (substr($phone, 0, 2) == '63') {
      // Set prefix
      $prefix = '(+63';
      // Suffix
      $suffix = substr($phone, 2);
      // If not mobile
      if (!static::isMobile($suffix)) {
        // Get length
        $len = strlen($suffix);
        // Get last 7 digits
        $lastSeven = static::getLastNDigits($suffix, 7);
        // If there's any prefix, append
        $prefix .= substr($suffix, 0, $len - 7);
        // Form
        $suffix = $lastSeven;
      }
      // Append ) to prefix
      $prefix .= ')';
      // Change
      $phone = $prefix . $suffix;
    } else {
      // Get length
      $length = strlen($phone);
      // If length is not 7
      if ($length != 7) {
        // Prepend 63 and fix
        $phone = static::fixPhoneNumber('63' . $phone);
      } else {
        // It's a local phone, nothing to do
      }
    }
    // Return phone
    return $phone;
  }

  /**
   * Check if phone number is mobile
   */
  static function isMobile($phone) {
    // If has network, then it's mobile
    return (Chikka::getNetwork($phone) !== false);
  }

  // Get last N digits
  static function getLastNDigits($number, $n) {
    // Get length
    $len = strlen($number);
    // If length is less than $n, return false
    if ($len < $n) return false;
    // Get last $n digits
    return substr($number, $len - $n);
  }
}