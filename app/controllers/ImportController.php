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

    // Get url
    $url = trim(Input::get('url'));

    ?>
    <form method="get" action="<?php action('ImportController@tripadvisor'); ?>">
      <input type="text" name="url" value="<?php echo e($url); ?>" />
      <button type="submit">Load</button>
    </form>
    <?php

    // If there's URL
    if ($url) {
      // Get info
      $info = $this->extractTripAdvisor($url);
      // Dump
      echo '<pre>',print_r($info, true),'</pre>';
    }
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
    // If there's HTML, get individual contents
    $establishment = array(
      // Get name
      'name'=> static::between($html, '<div class="warLocName">', '</div>'),
      // Get address
      'address'=> str_replace(' |', ',', static::between($html, '<div class="addr">', '</div>')),
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
      if ($mobiles) $contacts['mobiles'] = $mobiles;
      // If there's telephones
      if ($telephones) $contacts['telephones'] = $telephones;
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
        $contacts['emails'] = array($email);
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