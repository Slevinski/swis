<?php
/**
 * Exhaustive Unit Testing for range2regex
 * 
 * This file is part of SWIS: the SignWriting Icon Server.
 * 
 * SWIS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * SWIS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with SWIS.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * END Copyright
 *  
 * @copyright 2007-2012 Stephen E Slevinski Jr 
 * @author Steve Slevinski (slevin@signpuddle.net)  
 * @version 1
 * @section License 
 *   GPL 3, http://www.opensource.org/licenses/gpl-3.0.html
 * @brief Exhaustive test library for range2regex function
 * @file
 *   
 */

/** @defgroup r2r range2regex function
 *  Exhaustive unit testing for the range2regex function
 *  @ingroup test
 */

include 'fsw.php';
/** 
 * @brief Unit Testing class for range2regex function
 */
class R2RTest extends PHPUnit_Framework_TestCase {

  //with increment of 7 for each...
  //Time: 53 seconds, Memory: 2.75Mb
  //OK (3 tests, 608,927 assertions)
  
  //with increment of 1 for each...
  //Time: 05:17:02, Memory: 2.75Mb
  //OK (3 tests, 201,421,457 assertions)



  //! @ingroup r2r
  public function test_range2regex_test() {
    //replace specific values below to test specific hex range
    $lo = hexdec('100');
    $test = hexdec('101');
    $hi = hexdec('1e2');
    $v = ($test >= $lo) && ($test <= $hi);
    $re = '/' . range2regex(dechex($lo),dechex($hi),'hex') . '/';
    $result = preg_match($re,dechex($test),$matches);
    if ($v){
      $this->assertEquals(1,$result,dechex($lo) . ' ' . dechex($test) . ' ' . dechex($hi) . ' for ' . $re);
    } else {
      $this->assertEquals(0,$result, 'not ' . dechex($lo) . ' ' . dechex($test) . ' ' . dechex($hi) . ' for ' . $re);
    }
  }

  //! @ingroup r2r
  public function test_range2regex_dec() {
    $start = 250;
    $end = 750;
    $inc = 7;  //change increment for more detail, may be slow...
    for ($lo=$start;$lo<$end;$lo+=$inc){
      for ($hi=$lo;$hi<$end;$hi+=$inc){
        for ($test=$start;$test<$end;$test+=$inc){
          $v = ($test >= $lo) && ($test <= $hi);
          $re = '/' . range2regex($lo,$hi) . '/';
          $result = preg_match($re,$test,$matches);
          if ($v){
            $this->assertEquals($result,1,$lo . ' ' . $test . ' ' . $hi . ' for ' . $re);
          } else {
            $this->assertEquals($result,0, 'not ' . $lo . ' ' . $test . ' ' . $hi . ' for ' . $re);
          }
        }
      }
    }
  }

  //! @ingroup r2r
  public function test_range2regex_hex() {
    $start = hexdec('100');
    $end = hexdec('38c');  //1 after laast
    $inc = 7;  //change increment for more detail, may be slow...
    for ($lo=$start;$lo<$end;$lo+=$inc){
      for ($hi=$lo;$hi<$end;$hi+=$inc){
        for ($test=$start;$test<$end;$test+=$inc){
          $v = ($test >= $lo) && ($test <= $hi);
          $re = '/' . range2regex(dechex($lo),dechex($hi),'hex') . '/';
          $result = preg_match($re,dechex($test),$matches);
          if ($v){
            $this->assertEquals(1,$result,dechex($lo) . ' ' . dechex($test) . ' ' . dechex($hi) . ' for ' . $re);
          } else {
            $this->assertEquals(0,$result, 'not ' . dechex($lo) . ' ' . dechex($test) . ' ' . dechex($hi) . ' for ' . $re);
          }
        }
      }
    }
  }

}
?>
