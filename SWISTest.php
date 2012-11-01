<?php
/**
 * Unit Testing for the SignWriting Icon Server
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
 * @brief Test library for SWIS
 * @file
 *   
 */

/** @defgroup test Unit Testing
 *  Unit Testing
 */

/** @defgroup bswphp bsw.php Tests
 *  Unit testing for the bsw.php file
 *  @ingroup test
 */

/** @defgroup cswphp csw.php Tests
 *  Unit testing for the csw.php file
 *  @ingroup test
 */

/** @defgroup kswphp ksw.php Tests
 *  Unit testing for the ksw.php file
 *  @ingroup test
 */

/** @defgroup mswphp msw.php Tests
 *  Unit testing for the msw.php file
 *  @ingroup test
 */

include 'msw.php';
/** 
 * @brief Unit Testing class for bsw.php and csw.php
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class SWISTest extends PHPUnit_Framework_TestCase {
  //! @ingroup bswphp
  public function test_base2view() {
    $this->assertEquals('10000',base2view('100'));
    $this->assertEquals('1e010',base2view('1e0'));
  }

  //! @ingroup bswphp
  public function test_base2group() {
    $this->assertEquals('100',base2group('100'));
    $this->assertEquals('100',base2group('10d'));
    $this->assertEquals('10e',base2group('10f'));
    $this->assertEquals('10e',base2group('110'));
    $this->assertEquals('37f',base2group('386'));
    $this->assertEquals('387',base2group('387'));
    $this->assertEquals('387',base2group('388'));
  }

  //! @ingroup bswphp
  public function test_isSymGrp() {
    $this->assertTrue(isSymGrp('100'));
    $this->assertTrue(isSymGrp('10e'));
    $this->assertTrue(isSymGrp('387'));
    $this->assertFalse(isSymGrp('101'));
    $this->assertFalse(isSymGrp('110'));
    $this->assertFalse(isSymGrp('386'));
    $this->assertFalse(isSymGrp('388'));
  }

  //! @ingroup bswphp
  public function test_base2cat() {
    $this->assertEquals('100',base2cat('100'));
    $this->assertEquals('100',base2cat('10d'));
    $this->assertEquals('100',base2cat('10f'));
    $this->assertEquals('100',base2cat('110'));
    $this->assertEquals('37f',base2cat('386'));
    $this->assertEquals('387',base2cat('387'));
    $this->assertEquals('387',base2cat('388'));
  }

  //! @ingroup bswphp
  public function test_isKey() {
    $this->assertTrue(isKey('10000'));
    $this->assertTrue(isKey('10e00'));
    $this->assertTrue(isKey('38700'));
    $this->assertTrue(isKey('38700'));
    $this->assertFalse(isKey(''));
    $this->assertFalse(isKey('3870'));
    $this->assertFalse(isKey('38760'));
    $this->assertFalse(isKey('3870g'));
  }

  //! @ingroup bswphp
  public function test_validKey() {
    $this->assertTrue(validKey('10000'));
    $this->assertTrue(validKey('10e00'));
    $this->assertTrue(validKey('38700'));
    $this->assertTrue(validKey('38700'));
    $this->assertFalse(validKey(''));
    $this->assertFalse(validKey('3870'));
    $this->assertFalse(validKey('38760'));
    $this->assertFalse(validKey('387a0'));
    $this->assertFalse(validKey('3870g'));
    $this->assertFalse(validKey('3875f'));
    $this->assertFalse(validKey('14d00'));
  }

  //! @ingroup bswphp
  public function test_loadSymbolGroups() {
    $sg = loadSymbolGroups();
    $this->assertEquals(30,count($sg));
    $this->assertEquals(256,$sg[256]['code']);
  }

  //! @ingroup bswphp
  public function test_loadBaseSymbols() {
    $bs = loadBaseSymbols();
    $this->assertEquals(652,count($bs));
    $this->assertEquals(256,$bs[256]['code']);
  }

  //! @ingroup bswphp
  public function test_key2code() {
    $this->assertEquals(1,key2code('10000'));
    $this->assertEquals(2,key2code('10001'));
    $this->assertEquals(16,key2code('1000f'));
    $this->assertEquals(17,key2code('10010'));
    $this->assertEquals(97,key2code('10100'));
  }

  //! @ingroup bswphp
  public function test_code2key() {
    $this->assertEquals('10000',code2key(1));
    $this->assertEquals('10001',code2key(2));
    $this->assertEquals('1000f',code2key(16));
    $this->assertEquals('10010',code2key(17));
    $this->assertEquals('1005f',code2key(96));
    $this->assertEquals('10100',code2key(97));
  }

  //! @ingroup bswphp
  public function test_isISWA() {
    $this->assertTrue(isISWA('100'),'hand');
    $this->assertTrue(isISWA('204'),'hand');
    $this->assertTrue(isISWA('205'),'move');
    $this->assertTrue(isISWA('2f6'),'move');
    $this->assertTrue(isISWA('2f7'),'dyn');
    $this->assertTrue(isISWA('2fe'),'dyn');
    $this->assertTrue(isISWA('2ff'),'head');
    $this->assertTrue(isISWA('36c'),'head');
    $this->assertTrue(isISWA('36d'),'trunk');
    $this->assertTrue(isISWA('375'),'trunk');
    $this->assertTrue(isISWA('376'),'limb');
    $this->assertTrue(isISWA('37e'),'limb');
    $this->assertTrue(isISWA('37f'),'loc');
    $this->assertTrue(isISWA('386'),'loc');
    $this->assertTrue(isISWA('387'),'punc');
    $this->assertTrue(isISWA('38b'),'punc');
    $this->assertFalse(isISWA(''),'empty');
    $this->assertFalse(isISWA('38c'),'bs 652');
    $this->assertFalse(isISWA('400'),'400');
  }

  //! @ingroup bswphp
  public function test_isWrit() {
    $this->assertTrue(isWrit('100'),'hand');
    $this->assertTrue(isWrit('204'),'hand');
    $this->assertTrue(isWrit('205'),'move');
    $this->assertTrue(isWrit('2f6'),'move');
    $this->assertTrue(isWrit('2f7'),'dyn');
    $this->assertTrue(isWrit('2fe'),'dyn');
    $this->assertTrue(isWrit('2ff'),'head');
    $this->assertTrue(isWrit('36c'),'head');
    $this->assertTrue(isWrit('36d'),'trunk');
    $this->assertTrue(isWrit('375'),'trunk');
    $this->assertTrue(isWrit('376'),'limb');
    $this->assertTrue(isWrit('37e'),'limb');
    $this->assertFalse(isWrit('37f'),'loc');
    $this->assertFalse(isWrit('386'),'loc');
    $this->assertFalse(isWrit('387'),'punc');
    $this->assertFalse(isWrit('38b'),'punc');
    $this->assertFalse(isWrit(''),'empty');
    $this->assertFalse(isWrit('38c'),'bs 652');
    $this->assertFalse(isWrit('400'),'400');
  }

  //! @ingroup bswphp
  public function test_isHand() {
    $this->assertTrue(isHand('100'),'hand');
    $this->assertTrue(isHand('204'),'hand');
    $this->assertFalse(isHand('205'),'move');
    $this->assertFalse(isHand('2f6'),'move');
    $this->assertFalse(isHand('2f7'),'dyn');
    $this->assertFalse(isHand('2fe'),'dyn');
    $this->assertFalse(isHand('2ff'),'head');
    $this->assertFalse(isHand('36c'),'head');
    $this->assertFalse(isHand('36d'),'trunk');
    $this->assertFalse(isHand('375'),'trunk');
    $this->assertFalse(isHand('376'),'limb');
    $this->assertFalse(isHand('37e'),'limb');
    $this->assertFalse(isHand('37f'),'loc');
    $this->assertFalse(isHand('386'),'loc');
    $this->assertFalse(isHand('387'),'punc');
    $this->assertFalse(isHand('38b'),'punc');
    $this->assertFalse(isHand(''),'empty');
    $this->assertFalse(isHand('38c'),'bs 652');
    $this->assertFalse(isHand('400'),'400');
  }
 
  //! @ingroup bswphp
  public function test_isMove() {
    $this->assertFalse(isMove('100'),'hand');
    $this->assertFalse(isMove('204'),'hand');
    $this->assertTrue(isMove('205'),'move');
    $this->assertTrue(isMove('2f6'),'move');
    $this->assertFalse(isMove('2f7'),'dyn');
    $this->assertFalse(isMove('2fe'),'dyn');
    $this->assertFalse(isMove('2ff'),'head');
    $this->assertFalse(isMove('36c'),'head');
    $this->assertFalse(isMove('36d'),'trunk');
    $this->assertFalse(isMove('375'),'trunk');
    $this->assertFalse(isMove('376'),'limb');
    $this->assertFalse(isMove('37e'),'limb');
    $this->assertFalse(isMove('37f'),'loc');
    $this->assertFalse(isMove('386'),'loc');
    $this->assertFalse(isMove('387'),'punc');
    $this->assertFalse(isMove('38b'),'punc');
    $this->assertFalse(isMove(''),'empty');
    $this->assertFalse(isMove('38c'),'bs 652');
    $this->assertFalse(isMove('400'),'400');
  }
 
  //! @ingroup bswphp
  public function test_isDyn() {
    $this->assertFalse(isDyn('100'),'hand');
    $this->assertFalse(isDyn('204'),'hand');
    $this->assertFalse(isDyn('205'),'move');
    $this->assertFalse(isDyn('2f6'),'move');
    $this->assertTrue(isDyn('2f7'),'dyn');
    $this->assertTrue(isDyn('2fe'),'dyn');
    $this->assertFalse(isDyn('2ff'),'head');
    $this->assertFalse(isDyn('36c'),'head');
    $this->assertFalse(isDyn('36d'),'trunk');
    $this->assertFalse(isDyn('375'),'trunk');
    $this->assertFalse(isDyn('376'),'limb');
    $this->assertFalse(isDyn('37e'),'limb');
    $this->assertFalse(isDyn('37f'),'loc');
    $this->assertFalse(isDyn('386'),'loc');
    $this->assertFalse(isDyn('387'),'punc');
    $this->assertFalse(isDyn('38b'),'punc');
    $this->assertFalse(isDyn(''),'empty');
    $this->assertFalse(isDyn('38c'),'bs 652');
    $this->assertFalse(isDyn('400'),'400');
  }

  //! @ingroup bswphp
  public function test_isHead() {
    $this->assertFalse(isHead('100'),'hand');
    $this->assertFalse(isHead('204'),'hand');
    $this->assertFalse(isHead('205'),'move');
    $this->assertFalse(isHead('2f6'),'move');
    $this->assertFalse(isHead('2f7'),'dyn');
    $this->assertFalse(isHead('2fe'),'dyn');
    $this->assertTrue(isHead('2ff'),'head');
    $this->assertTrue(isHead('36c'),'head');
    $this->assertFalse(isHead('36d'),'trunk');
    $this->assertFalse(isHead('375'),'trunk');
    $this->assertFalse(isHead('376'),'limb');
    $this->assertFalse(isHead('37e'),'limb');
    $this->assertFalse(isHead('37f'),'loc');
    $this->assertFalse(isHead('386'),'loc');
    $this->assertFalse(isHead('387'),'punc');
    $this->assertFalse(isHead('38b'),'punc');
    $this->assertFalse(isHead(''),'empty');
    $this->assertFalse(isHead('38c'),'bs 652');
    $this->assertFalse(isHead('400'),'400');
  }

  //! @ingroup bswphp
  public function test_isTrunk() {
    $this->assertFalse(isTrunk('100'),'hand');
    $this->assertFalse(isTrunk('204'),'hand');
    $this->assertFalse(isTrunk('205'),'move');
    $this->assertFalse(isTrunk('2f6'),'move');
    $this->assertFalse(isTrunk('2f7'),'dyn');
    $this->assertFalse(isTrunk('2fe'),'dyn');
    $this->assertFalse(isTrunk('2ff'),'head');
    $this->assertFalse(isTrunk('36c'),'head');
    $this->assertTrue(isTrunk('36d'),'trunk');
    $this->assertTrue(isTrunk('375'),'trunk');
    $this->assertFalse(isTrunk('376'),'limb');
    $this->assertFalse(isTrunk('37e'),'limb');
    $this->assertFalse(isTrunk('37f'),'loc');
    $this->assertFalse(isTrunk('386'),'loc');
    $this->assertFalse(isTrunk('387'),'punc');
    $this->assertFalse(isTrunk('38b'),'punc');
    $this->assertFalse(isTrunk(''),'empty');
    $this->assertFalse(isTrunk('38c'),'bs 652');
    $this->assertFalse(isTrunk('400'),'400');
  }

  //! @ingroup bswphp
  public function test_isLimb() {
    $this->assertFalse(isLimb('100'),'hand');
    $this->assertFalse(isLimb('204'),'hand');
    $this->assertFalse(isLimb('205'),'move');
    $this->assertFalse(isLimb('2f6'),'move');
    $this->assertFalse(isLimb('2f7'),'dyn');
    $this->assertFalse(isLimb('2fe'),'dyn');
    $this->assertFalse(isLimb('2ff'),'head');
    $this->assertFalse(isLimb('36c'),'head');
    $this->assertFalse(isLimb('36d'),'trunk');
    $this->assertFalse(isLimb('375'),'trunk');
    $this->assertTrue(isLimb('376'),'limb');
    $this->assertTrue(isLimb('37e'),'limb');
    $this->assertFalse(isLimb('37f'),'loc');
    $this->assertFalse(isLimb('386'),'loc');
    $this->assertFalse(isLimb('387'),'punc');
    $this->assertFalse(isLimb('38b'),'punc');
    $this->assertFalse(isLimb(''),'empty');
    $this->assertFalse(isLimb('38c'),'bs 652');
    $this->assertFalse(isLimb('400'),'400');
  }
 
  //! @ingroup bswphp
  public function test_isLoc() {
    $this->assertFalse(isLoc('100'),'hand');
    $this->assertFalse(isLoc('204'),'hand');
    $this->assertFalse(isLoc('205'),'move');
    $this->assertFalse(isLoc('2f6'),'move');
    $this->assertFalse(isLoc('2f7'),'dyn');
    $this->assertFalse(isLoc('2fe'),'dyn');
    $this->assertFalse(isLoc('2ff'),'head');
    $this->assertFalse(isLoc('36c'),'head');
    $this->assertFalse(isLoc('36d'),'trunk');
    $this->assertFalse(isLoc('375'),'trunk');
    $this->assertFalse(isLoc('376'),'limb');
    $this->assertFalse(isLoc('37e'),'limb');
    $this->assertTrue(isLoc('37f'),'loc');
    $this->assertTrue(isLoc('386'),'loc');
    $this->assertFalse(isLoc('387'),'punc');
    $this->assertFalse(isLoc('38b'),'punc');
    $this->assertFalse(isLoc(''),'empty');
    $this->assertFalse(isLoc('38c'),'bs 652');
    $this->assertFalse(isLoc('400'),'400');
  }

  //! @ingroup bswphp
  public function test_isPunc() {
    $this->assertFalse(isPunc('100'),'hand');
    $this->assertFalse(isPunc('204'),'hand');
    $this->assertFalse(isPunc('205'),'move');
    $this->assertFalse(isPunc('2f6'),'move');
    $this->assertFalse(isPunc('2f7'),'dyn');
    $this->assertFalse(isPunc('2fe'),'dyn');
    $this->assertFalse(isPunc('2ff'),'head');
    $this->assertFalse(isPunc('36c'),'head');
    $this->assertFalse(isPunc('36d'),'trunk');
    $this->assertFalse(isPunc('375'),'trunk');
    $this->assertFalse(isPunc('376'),'limb');
    $this->assertFalse(isPunc('37e'),'limb');
    $this->assertFalse(isPunc('37f'),'loc');
    $this->assertFalse(isPunc('386'),'loc');
    $this->assertTrue(isPunc('387'),'punc');
    $this->assertTrue(isPunc('38b'),'punc');
    $this->assertFalse(isPunc(''),'empty');
    $this->assertFalse(isPunc('38c'),'bs 652');
    $this->assertFalse(isPunc('400'),'400');
  }
  
  //! @ingroup bswphp
  public function test_char2fill() {
    $this->assertEquals('0',char2fill('110'));
    $this->assertEquals('5',char2fill('115'));
  }

  //! @ingroup bswphp
  public function test_fill2char() {
    $this->assertEquals('110',fill2char('0'));
    $this->assertEquals('115',fill2char('5'));
  }

  //! @ingroup bswphp
  public function test_char2rot() {
    $this->assertEquals('0',char2rot('120'));
    $this->assertEquals('5',char2rot('125'));
    $this->assertEquals('a',char2rot('12a'));
    $this->assertEquals('f',char2rot('12f'));
  }

  //! @ingroup bswphp
  public function test_rot2char() {
    $this->assertEquals('120',rot2char('0'));
    $this->assertEquals('125',rot2char('5'));
    $this->assertEquals('12a',rot2char('a'));
    $this->assertEquals('12f',rot2char('f'));
  }

  //! @ingroup bswphp
  public function test_key2bsw() {
    $this->assertEquals('130110120',key2bsw('10000'));
    $this->assertEquals('13011512f',key2bsw('1005f'));
  }

  //! @ingroup bswphp
  public function test_bsw2key() {
    $this->assertEquals('10000',bsw2key('130110120'));
    $this->assertEquals('1005f',bsw2key('13011512f'));
  }
  
   //! @ingroup bswphp
  public function test_koord2str() {
    $this->assertEquals('0x0',koord2str(0,0));
    $this->assertEquals('n125xn125',koord2str(-125,-125));
    $this->assertEquals('125x125',koord2str(125,125));
  }

  //! @ingroup bswphp
  public function test_str2koord() {
    $this->assertEquals(array(0,0), str2koord('0x0'));
    $this->assertEquals(array(125,125), str2koord('125x125'));
    $this->assertEquals(array(-125,-125), str2koord('n125xn125'));
  }

  //! @ingroup bswphp
  public function test_coord2str() {
    $this->assertEquals('500x500',coord2str(0,0));
    $this->assertEquals('375x375',coord2str(-125,-125));
    $this->assertEquals('625x625',coord2str(125,125));
  }

  //! @ingroup bswphp
  public function test_str2coord() {
    $this->assertEquals(array(0,0), str2coord('500x500'));
    $this->assertEquals(array(125,125), str2coord('625x625'));
    $this->assertEquals(array(-125,-125), str2coord('375x375'));
  }

  //! @ingroup bswphp
  public function test_num2bsw() {
    $this->assertEquals('800', num2bsw(0));
    $this->assertEquals('706', num2bsw(-250));
    $this->assertEquals('8f9', num2bsw(249));
  }

  //! @ingroup bswphp
  public function test_bsw2num() {
    $this->assertEquals(0,bsw2num('800'));
    $this->assertEquals(-250,bsw2num('706'));
    $this->assertEquals(-16,bsw2num('7f0'));
    $this->assertEquals(249,bsw2num('8f9'));
  }

  //! @ingroup bswphp
  public function test_coord2bsw() {
    $this->assertEquals('7068f9', coord2bsw(array(-250,249)));
  }

  //! @ingroup bswphp
  public function test_bsw2coord() {
    $this->assertEquals(array(-250,249),bsw2coord('7068f9'));
    $this->assertEquals(array(-16,-16),bsw2coord('7f07f0'));
    
  }

 //! @ingroup cswphp
  public function test_dec2utf() {
    $utf = dec2utf(0);
    $val = unpack("N",$utf);
    $val = dechex($val[1]);
    $this->assertEquals('f3b08080',$val);
    $utf = dec2utf(hexdec('d800'));
    $val = unpack("N",$utf);
    $val = dechex($val[1]);
    $this->assertEquals('f3bda080',$val);
  }

  //! @ingroup cswphp
  public function test_char2utf() {
    $utf = char2utf('100');
    $val = unpack("N",$utf);
    $val = dechex($val[1]);
    $this->assertEquals('f3bda080',$val);
  }

  //! @ingroup cswphp
  public function test_char2unicode() {
    $this->assertEquals('FD800',char2unicode('100'));
  }

  //! @ingroup cswphp
  public function test_bsw2csw() {
    $utf = bsw2csw('13011512f');
    $vals = str_split($utf,strlen($utf)/3);
    $val = unpack("N",$vals[0]);
    $val = dechex($val[1]);
    $this->assertEquals('f3bda0b0',$val);
    $val = unpack("N",$vals[1]);
    $val = dechex($val[1]);
    $this->assertEquals('f3bda095',$val);
    $val = unpack("N",$vals[2]);
    $val = dechex($val[1]);
    $this->assertEquals('f3bda0af',$val);
  }

  //! @ingroup cswphp
  public function test_utf2char() {
    $this->assertEquals('130',utf2char(char2utf('130')));
    $this->assertEquals('115',utf2char(char2utf('115')));
    $this->assertEquals('12f',utf2char(char2utf('12f')));
  }

  //! @ingroup cswphp
  public function test_csw2bsw() {
    $this->assertEquals('13011512f',csw2bsw(bsw2csw('13011512f')));
  }

  //! @ingroup cswphp
  public function test_num2utf() {
    $this->assertEquals('FDF00', char2unicode(utf2char(num2utf(0))));
    $this->assertEquals('FDE06', char2unicode(utf2char(num2utf(-250))));
    $this->assertEquals('FDFF9', char2unicode(utf2char(num2utf(249))));
  }

  //! @ingroup cswphp
  public function test_coord2utf() {
    $this->assertEquals('7068f9', csw2bsw(coord2utf(array(-250,249))));
  }

  //! @ingroup cswphp
  public function test_cswText() {
    //empty signboxes
    $this->assertTrue(cswText(bsw2csw('101800800')));
    $this->assertTrue(cswText(bsw2csw('102800800')));
    $this->assertTrue(cswText(bsw2csw('103800800')));
    $this->assertTrue(cswText(bsw2csw('104800800')));
    //errors
    $this->assertFalse(cswText(bsw2csw('100800800')));
    $this->assertFalse(cswText(bsw2csw('105800800')));
    //longer strings
    $this->assertTrue(cswText(bsw2csw('103850850130110120790790')));
    $this->assertTrue(cswText(bsw2csw('100130110120103850850130110120790790')));
    //empty sequence
    $this->assertFalse(cswText(bsw2csw('100103850850130110120790790')));
  }

  //! @ingroup kswphp
  public function test_kswRaw() {
    //empty signboxes
    $this->assertTrue(kswRaw('B'),'empty B');
    $this->assertTrue(kswRaw('L'),'empty L');
    $this->assertTrue(kswRaw('M'),'empty M');
    $this->assertTrue(kswRaw('R'),'empty R');
    $this->assertTrue(kswRaw('BS10000n10xn10'),'non-empty B');
    $this->assertTrue(kswRaw('LS10000n10xn10'),'non-empty L');
    $this->assertTrue(kswRaw('MS10000n10xn10'),'non-empty M');
    $this->assertTrue(kswRaw('RS10000n10xn10'),'non-empty R');
    $this->assertTrue(kswRaw('AS10000BS10000n10xn10'),'non-empty AB');
    $this->assertTrue(kswRaw('AS10000LS10000n10xn10'),'non-empty AL');
    $this->assertTrue(kswRaw('AS10000MS10000n10xn10'),'non-empty AM');
    $this->assertTrue(kswRaw('AS10000RS10000n10xn10'),'non-empty AR');
  }

  //! @ingroup kswphp
  public function test_kswExpand() {
    //empty signboxes
    $this->assertTrue(kswExpand('B'),'empty B');
    $this->assertTrue(kswExpand('L'),'empty L');
    $this->assertTrue(kswExpand('M'),'empty M');
    $this->assertTrue(kswExpand('R'),'empty R');
    $this->assertTrue(kswExpand('BS1000010x10xn10xn10'),'non-empty B');
    $this->assertTrue(kswExpand('LS1000010x10xn10xn10'),'non-empty L');
    $this->assertTrue(kswExpand('MS1000010x10xn10xn10'),'non-empty M');
    $this->assertTrue(kswExpand('RS1000010x10xn10xn10'),'non-empty R');
    $this->assertTrue(kswExpand('AS10000BS1000010x10xn10xn10'),'non-empty AB');
    $this->assertTrue(kswExpand('AS10000LS1000010x10xn10xn10'),'non-empty AL');
    $this->assertTrue(kswExpand('AS10000MS1000010x10xn10xn10'),'non-empty AM');
    $this->assertTrue(kswExpand('AS10000RS1000010x10xn10xn10'),'non-empty AR');
  }

  //! @ingroup kswphp
  public function test_kswLayout() {
    //empty signboxes
    $this->assertTrue(kswLayout('B0x0'),'empty B');
    $this->assertTrue(kswLayout('L0x0'),'empty L');
    $this->assertTrue(kswLayout('M0x0'),'empty M');
    $this->assertTrue(kswLayout('R0x0'),'empty R');
    $this->assertTrue(kswLayout('B10x10S10000n10xn10'),'non-empty B');
    $this->assertTrue(kswLayout('L10x10S10000n10xn10'),'non-empty L');
    $this->assertTrue(kswLayout('M10x10S10000n10xn10'),'non-empty M');
    $this->assertTrue(kswLayout('R10x10S10000n10xn10'),'non-empty R');
    $this->assertTrue(kswLayout('AS10000B10x10S10000n10xn10'),'non-empty AB');
    $this->assertTrue(kswLayout('AS10000L10x10S10000n10xn10'),'non-empty AL');
    $this->assertTrue(kswLayout('AS10000M10x10S10000n10xn10'),'non-empty AM');
    $this->assertTrue(kswLayout('AS10000R10x10S10000n10xn10'),'non-empty AR');
  }

  //! @ingroup kswphp
  public function test_kswPanel() {
    //empty signboxes
    $this->assertTrue(kswPanel('D10x10_B0x0'),'empty B');
    $this->assertTrue(kswPanel('D10x10_L0x0'),'empty L');
    $this->assertTrue(kswPanel('D10x10_M0x0'),'empty M');
    $this->assertTrue(kswPanel('D10x10_R0x0'),'empty R');
    $this->assertTrue(kswPanel('D20x20_B10x10S10000n10xn10'),'non-empty B');
    $this->assertTrue(kswPanel('D20x20_L10x10S10000n10xn10'),'non-empty L');
    $this->assertTrue(kswPanel('D20x20_M10x10S10000n10xn10'),'non-empty M');
    $this->assertTrue(kswPanel('D20x20_R10x10S10000n10xn10'),'non-empty R');
  }

  //! @ingroup kswphp
  public function test_ksw2key() {
    $this->assertEquals('S10000',ksw2key('BS10000n10xn10'),'non-empty B');
    $this->assertEquals('S10000',ksw2key('LS10000n10xn10'),'non-empty L');
    $this->assertEquals('S10000',ksw2key('MS10000n10xn10'),'non-empty M');
    $this->assertEquals('S10000',ksw2key('RS10000n10xn10'),'non-empty R');
    $this->assertEquals('S10000S1035f',ksw2key('AS10000BS10000n10xn10S1035f10x10'),'non-empty AB');
    $this->assertEquals('S10000S1035f',ksw2key('AS10000LS10000n10xn10S1035f10x10'),'non-empty AL');
    $this->assertEquals('S10000S1035f',ksw2key('AS10000MS10000n10xn10S1035f10x10'),'non-empty AM');
    $this->assertEquals('S10000S1035f',ksw2key('AS10000RS10000n10xn10S1035f10x10'),'non-empty AR');
  }

  //! @ingroup kswphp
  public function test_ksw2seq(){
    $this->assertEquals('',ksw2seq('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32'));
    $this->assertEquals('S1870aS18701S2e734',ksw2seq('AS1870aS18701S2e734M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32'));
  }

  //! @ingroup kswphp
  public function test_ksw2cluster(){
    $syms = ksw2cluster('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32');
    $this->assertEquals('18x33',$syms[0][1]);
    $this->assertEquals('S1870a',$syms[1][0]);
    $this->assertEquals('n11x15',$syms[1][1]);
    $this->assertEquals('S18701',$syms[2][0]);
    $this->assertEquals('n18xn10',$syms[2][1]);
    $this->assertEquals('S20500',$syms[3][0]);
    $this->assertEquals('8xn4',$syms[3][1]);
    $this->assertEquals('S2e734',$syms[4][0]);
    $this->assertEquals('0xn32',$syms[4][1]);

    $syms = ksw2cluster('MS1870an11x15S18701n18xn10S205008xn4S2e7340xn32');
    $this->assertEquals('',$syms[0][1]);
    $this->assertEquals('S1870a',$syms[1][0]);
    $this->assertEquals('n11x15',$syms[1][1]);
    $this->assertEquals('S18701',$syms[2][0]);
    $this->assertEquals('n18xn10',$syms[2][1]);
    $this->assertEquals('S20500',$syms[3][0]);
    $this->assertEquals('8xn4',$syms[3][1]);
    $this->assertEquals('S2e734',$syms[4][0]);
    $this->assertEquals('0xn32',$syms[4][1]);
    
    $syms = ksw2cluster('S38800n36xn4');
    $this->assertEquals('36x4',$syms[0][1]);
    $this->assertEquals('S38800',$syms[1][0]);
    $this->assertEquals('n36xn4',$syms[1][1]);
    
  }
  
  //! @ingroup kswphp
  public function test_expand2cluster(){
    $syms = expand2cluster('MS1870a29x18xn11x15S1870124x24xn18xn10S2050010x11x8xn4S2e73416x25x0xn32');
    $this->assertEquals('',$syms[0][1]);
    $this->assertEquals('S1870a',$syms[1][0]);
    $this->assertEquals('29x18xn11x15',$syms[1][1]);
    $this->assertEquals('S18701',$syms[2][0]);
    $this->assertEquals('24x24xn18xn10',$syms[2][1]);
    $this->assertEquals('S20500',$syms[3][0]);
    $this->assertEquals('10x11x8xn4',$syms[3][1]);
    $this->assertEquals('S2e734',$syms[4][0]);
    $this->assertEquals('16x25x0xn32',$syms[4][1]);
  }

//! @ingroup kswphp
  public function test_offset2cluster(){
    $syms = offset2cluster('M115x49S14c20n19xn29S271063xn11');
    $this->assertEquals('S14c20',$syms[0][0]);
    $this->assertEquals('96x20',$syms[0][1]);
    $this->assertEquals('S14c20',$syms[0][0]);
    $this->assertEquals('118x38',$syms[1][1]);
  }
  
  //! @ingroup kswphp
  public function test_panel2cluster(){
    $syms = panel2cluster('D230x400_M115x49S14c20n19xn29S271063xn11_M115x150S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_M115x207S38800n36xn4');
    $this->assertEquals('230x400',$syms[0][1]);
    $this->assertEquals('S14c20',$syms[1][0]);
    $this->assertEquals('96x20',$syms[1][1]);
    $this->assertEquals('S27106',$syms[2][0]);
    $this->assertEquals('118x38',$syms[2][1]);
    $this->assertEquals('S1870a',$syms[3][0]);
    $this->assertEquals('104x165',$syms[3][1]);
    $this->assertEquals('S18701',$syms[4][0]);
    $this->assertEquals('97x140',$syms[4][1]);
    $this->assertEquals('S20500',$syms[5][0]);
    $this->assertEquals('123x146',$syms[5][1]);
    $this->assertEquals('S2e734',$syms[6][0]);
    $this->assertEquals('115x118',$syms[6][1]);
    $this->assertEquals('S38800',$syms[7][0]);
    $this->assertEquals('79x203',$syms[7][1]);
  }

  //! @ingroup kswphp
  public function test_cluster2ksw(){
    $syms = panel2cluster('D230x300_M115x49S14c20n19xn29S271063xn11_M115x150S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_M115x207S38800n36xn4');
    $this->assertEquals('M230x300S14c2096x20S27106118x38S1870a104x165S1870197x140S20500123x146S2e734115x118S3880079x203',cluster2ksw($syms));
  }

  //! @ingroup kswphp
  public function test_cluster2min(){
    $this->assertEquals(array(0,0),cluster2min(ksw2cluster('M230x300S14c2096x20S27106118x38S1870a104x165S1870197x140S20500123x146S2e734115x118S3880079x203')));
    $this->assertEquals(array(-19,-29),cluster2min(ksw2cluster('M115x49S14c20n19xn29S271063xn11')));
  }

  //! @ingroup kswphp
  public function test_cluster2max(){
    $this->assertEquals(array(230,300),cluster2max(ksw2cluster('M230x300S14c2096x20S27106118x38S1870a104x165S1870197x140S20500123x146S2e734115x118S3880079x203')));
    $this->assertEquals(array(115,49),cluster2max(ksw2cluster('M115x49S14c20n19xn29S271063xn11')));
  }

  //! @ingroup kswphp
  public function test_ksw2raw(){
    $this->assertEquals('MS1870an11x15S18701n18xn10S205008xn4S2e7340xn32',ksw2raw('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32'));
    $this->assertEquals('AS1870aS18701S2e734MS1870an11x15S18701n18xn10S205008xn4S2e7340xn32',ksw2raw('AS1870aS18701S2e734M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32'));
    $this->assertEquals('MS14c20n19xn29S271063xn11 MS1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800',ksw2raw('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4'));
  }

  //! @ingroup kswphp
  public function test_ksw2expand(){
    $this->assertEquals('MS1870a29x18xn11x15S1870124x24xn18xn10S2050010x11x8xn4S2e73416x25x0xn32',ksw2expand('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32'),'no seq with max');
    $this->assertEquals('AS1870aS18701S2e734MS1870a29x18xn11x15S1870124x24xn18xn10S2050010x11x8xn4S2e73416x25x0xn32',ksw2expand('AS1870aS18701S2e734M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32'),'seq with max');
    $this->assertEquals('MS14c2023x31xn19xn29S2710615x40x3xn11 MS1870a29x18xn11x15S1870124x24xn18xn10S2050010x11x8xn4S2e73416x25x0xn32 S3880072x8',ksw2expand('MS14c20n19xn29S271063xn11 MS1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800'),'multiple raw');
    $this->assertEquals('MS14c2023x31xn19xn29S2710615x40x3xn11 MS1870a29x18xn11x15S1870124x24xn18xn10S2050010x11x8xn4S2e73416x25x0xn32 S3880072x8',ksw2expand('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4'),'mult layout');
  }

  //! @ingroup kswphp
  public function test_raw2ksw(){
    $this->assertEquals('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32',raw2ksw('MS1870an11x15S18701n18xn10S205008xn4S2e7340xn32'));
    $this->assertEquals('AS1870aS18701S2e734M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32',raw2ksw('AS1870aS18701S2e734MS1870an11x15S18701n18xn10S205008xn4S2e7340xn32'));
    $this->assertEquals('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4',raw2ksw('MS14c20n19xn29S271063xn11 MS1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800'));
    //centered by head and trunk
    $iksw = 'M124x115S35700n124xn25S34700n92xn25S34700n12xn25S3440088xn25S35d00n45xn25S3610022xn25S35d0055xn25S15d3119x92S15d39n77x45S18041n13x37S36d00n29x22S2890c13x56S2f900n65x72S20e00n16x32';
    //centered by head for XY and trunk for X only
    $oksw = 'M124x122S35700n124xn18S34700n92xn18S34700n12xn18S3440088xn18S35d00n45xn18S3610022xn18S35d0055xn18S15d3119x99S15d39n77x52S18041n13x44S36d00n29x29S2890c13x63S2f900n65x79S20e00n16x39';
    $this->assertEquals($oksw,raw2ksw(ksw2raw($iksw)));
  }


  //! @ingroup kswphp
  public function test_crosshairs(){
    $this->assertEquals('M45x60S1870an11x15S18701n18xn10S205008xn4S2e7340xn32S37c00n1xn59S37c00n1x48S37c06n45xn1S37c0633xn1',crosshairs('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32'),'1');
    $this->assertEquals('M30x45S1870an11x15S18701n18xn10S205008xn4S2e7340xn32S37c00n1xn44S37c00n1x33S37c06n30xn1S37c0618xn1',crosshairs('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32',0),'2');
    $this->assertEquals('M30x45S1870an11x15S18701n18xn10S205008xn4S2e7340xn32S37c00n1xn44S37c00n1x33S37c06n30xn1S37c0618xn1',crosshairs('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32',0,array(-18,-32)),'3');
    $this->assertEquals('M40x55S1870an11x15S18701n18xn10S205008xn4S2e7340xn32S37c00n1xn54S37c00n1x43S37c06n40xn1S37c0628xn1',crosshairs('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32',10,array(-18,-32)),'4');
    $this->assertEquals('M30x45S1870an11x15S18701n18xn10S205008xn4S2e7340xn32S37c00n9xn44S37c00n9x33S37c06n30xn23S37c0618xn23',crosshairs('M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32',0,array(-10,-10)),'5');
  }

  //! @ingroup kswphp
  public function test_panelTrim(){
    $this->assertEquals('M36x96S14c20n19xn95S271063xn77S1870an11x50S18701n18x25S205008x31S2e7340x3S38800n36x88',panelTrim('D230x300_M115x49S14c20n19xn29S271063xn11_M115x150S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_M115x207S38800n36xn4'));
    $this->assertEquals('M36x84S14c20n19xn83S271063xn65S1870an11x46S18701n18x21S205008x27S2e7340xn1S38800n36x76',panelTrim('D102x571_M51x41S14c20n19xn29S271063xn11_M51x126S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_B51x175S38800n36xn4'));
  }

  //! @ingroup kswphp
  public function test_ksw2panel(){
    $this->assertEquals('D102x200_M51x49S14c20n19xn29S271063xn11_M51x139S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_B51x196S38800n36xn4',ksw2panel('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4',200));
    $this->assertEquals('D200x102_B39x51S14c20n19xn29S271063xn11_B115x51S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_B157x51S38802n4xn36',ksw2panel('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4',200,array("form"=>'row')));
    $ksw = 'B124x115S35700n124xn25S34700n92xn25S34700n12xn25S3440088xn25S35d00n45xn25S3610022xn25S35d0055xn25S15d3119x92S15d39n77x45S18041n13x37S36d00n29x22S2890c13x56S2f900n65x72S20e00n16x32 B83x106S34600n67xn17S33b00n34xn17S3440032xn17S36100n1xn17S15d39n27x47S15d3160x83S2f900n37x68S1152057x28S2a10a19x58 B71x66S3470033xn17S34700n35xn17S1005141x45S35700n68xn17S35d00n1xn17S2650122x44 B60x66S35700n61xn17S34700n32xn18S3470024xn18S35d00n5xn18S15d0an1x26S15d009x39S2060031x40 B130x51S35700n101xn17S34400n68xn17S34700n1xn17S35d00n34xn17S35c0033xn17S35d0066xn17S1001297x13S2f00098x31S3000566xn17S22a07117xn4 B135x55S35700n91xn17S34400n62xn17S3470025xn17S35d00n33xn17S35c00n4xn17S35d0455xn17S1001287x12S2f00098x35S3000555xn17S22a07122x1 B76x83S35c00n17xn17S35d0040xn17S35700n75xn17S34400n46xn17S3470012xn17S1001011x35S10018n28x42S22b0736x27S22b11n58x35S2fd04n10x77 B80x79S35c0044xn17S2fd04n1x73S35700n17xn17S3440013xn17S22b11n48x40S1001016x30S10018n17x37S22b0739x30S35d04n79xn17S34700n48xn17 B105x68S35700n77xn17S34400n48xn17S3470011xn17S35d04n19xn17S35d0441xn17S16d1173xn20S16d19n88xn25S2050080x5S20500n90xn1S2d70976x27S2d711n107x25S20336n93x53S2033270x49 B107x133S2fb0430x112S2050032x46S15a4036x61S15a4822x61S15a30n13x106S15a3079x106S2890a47x91S289121x92S35700n106xn17S34400n77xn17S34600n48xn17S34c0042xn17S35d04n18xn17S35d0012xn17S35c0071xn17S19220n56x58S2a20cn84x35 B64x105S35700n64xn18S34600n33xn18S35d00n2xn18S3470028xn18S17d1e37x17S22f0432x62S2100036x44S1851e39x80 B55x123S31600n35xn25S31400n1xn24S22b14n17x72S22b0428x62S2031024x30S20318n16x40S2034134x93S20349n11x102S36d00n22x20S2f900n15x59S2f90026x50S3715025x14S35730n27xn5S3461011xn7 B55x123S31600n35xn25S31400n1xn24S22b14n17x72S22b0428x62S2031024x30S20318n16x40S2034134x93S20349n11x102S36d00n22x20S2f900n15x59S2f90026x50S3715025x14S35730n27xn5S3461011xn7';
    $ksw = raw2ksw(ksw2raw($ksw));
    $kswd = 'D600x170_B144x33S35700n124xn18S34700n92xn18S34700n12xn18S3440088xn18S35d00n45xn18S3610022xn18S35d0055xn18S15d3119x99S15d39n77x52S18041n13x44S36d00n29x29S2890c13x63S2f900n65x79S20e00n16x39_B496x33S34600n67xn17S33b00n34xn17S3440032xn17S36100n1xn17S15d39n27x47S15d3160x83S2f900n37x68S1152057x28S2a10a19x58 D600x114_B88x33S3470033xn17S34700n35xn17S1005141x45S35700n68xn17S35d00n1xn17S2650122x44_B255x33S35700n61xn17S34700n32xn18S3470024xn18S35d00n5xn18S15d0an1x26S15d009x39S2060031x40_B451x33S35700n101xn17S34400n68xn17S34700n1xn17S35d00n34xn17S35c0033xn17S35d0066xn17S1001297x13S2f00098x31S3000566xn17S22a07117xn4 D600x130_B111x32S35700n91xn17S34400n62xn17S3470025xn17S35d00n33xn17S35c00n4xn17S35d0455xn17S1001287x12S2f00098x35S3000555xn17S22a07122x1_B503x32S35c00n17xn17S35d0040xn17S35700n75xn17S34400n46xn17S3470012xn17S1001011x35S10018n28x42S22b0736x27S22b11n58x35S2fd04n10x77 D600x134_B99x40S35c0044xn17S2fd04n1x73S35700n17xn17S3440013xn17S22b11n48x40S1001016x30S10018n17x37S22b0739x30S35d04n79xn17S34700n48xn17_B474x40S35700n77xn17S34400n48xn17S3470011xn17S35d04n19xn17S35d0441xn17S16d1173xn20S16d19n88xn25S2050080x5S20500n90xn1S2d70976x27S2d711n107x25S20336n93x53S2033270x49 D600x181_B126x33S2fb0430x112S2050032x46S15a4036x61S15a4822x61S15a30n13x106S15a3079x106S2890a47x91S289121x92S35700n106xn17S34400n77xn17S34600n48xn17S34c0042xn17S35d04n18xn17S35d0012xn17S35c0071xn17S19220n56x58S2a20cn84x35_B361x33S35700n64xn18S34600n33xn18S35d00n2xn18S3470028xn18S17d1e37x17S22f0432x62S2100036x44S1851e39x80_B524x33S31600n35xn18S31400n1xn17S22b14n17x79S22b0428x69S2031024x37S20318n16x47S2034134x100S20349n11x109S36d00n22x27S2f900n15x66S2f90026x57S3715025x21S35730n27x2S3461011x0 D600x178_B55x33S31600n35xn18S31400n1xn17S22b14n17x79S22b0428x69S2031024x37S20318n16x47S2034134x100S20349n11x109S36d00n22x27S2f900n15x66S2f90026x57S3715025x21S35730n27x2S3461011x0';
    $this->assertEquals($kswd,ksw2panel($ksw,600));
    $ksw = 'B60x66S35700n61xn17S34700n32xn18S3470024xn18S35d00n5xn18S15d0an1x26S15d009x39S2060031x40 B18x29S14c20n19xn29S271063xn11 B83x106S34600n67xn17S33b00n34xn17S3440032xn17S36100n1xn17S15d39n27x47S15d3160x83S2f900n37x68S1152057x28S2a10a19x58 B18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38802n4xn36';
    $kswd = 'D600x154_B81x33S35700n61xn17S34700n32xn18S3470024xn18S35d00n5xn18S15d0an1x26S15d009x39S2060031x40_B200x83S14c20n19xn29S271063xn11_B325x33S34600n67xn17S33b00n34xn17S3440032xn17S36100n1xn17S15d39n27x47S15d3160x83S2f900n37x68S1152057x28S2a10a19x58_B466x83S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_B508x83S38802n4xn36';
    $this->assertEquals($kswd,ksw2panel($ksw,600,array("signTop"=>20,"signBottom"=>20,"puncBottom"=>20,)));
    $ksw = 'M23x53S30a00n18xn17S10e008x23 M30x27S10021n7x6S10029n23x5S266060xn21S26612n29xn21S2fb00n8xn26 M18x77S30a00n18xn17S20320n36x22S20320n60x22S23c04n35x47S23c1cn68x47S2fb04n48x71 M72x79S30a00n18xn17S2032048x24S2032024x24S23c0449x49S23c1c16x49S2fb0436x73 S38700n37xn4 L18x52S30a00n18xn17S10008n39x22 L18x71S30a00n18xn17S20318n16x23S2411bn38x43 S38700n37xn4 R32x45S30a00n18xn17S1000017x15 R31x18S20e00n16xn4S26501n30xn18S15a364x6S1821d1xn8 S38800n36xn4';
    $panel = 'D900x134_B38x32S30a00n18xn17S10e008x23_B129x82S10021n7x6S10029n23x5S266060xn21S26612n29xn21S2fb00n8xn26_B266x32S30a00n18xn17S20320n36x22S20320n60x22S23c04n35x47S23c1cn68x47S2fb04n48x71_B341x32S30a00n18xn17S2032048x24S2032024x24S23c0449x49S23c1c16x49S2fb0436x73_B436x82S38702n4xn37_B528x32S30a00n18xn17S10008n39x22_B623x32S30a00n18xn17S20318n16x23S2411bn38x43_B664x82S38702n4xn37_B735x32S30a00n18xn17S1000017x15_B836x82S20e00n16xn4S26501n30xn18S15a364x6S1821d1xn8_B891x82S38802n4xn36';
    $this->assertEquals($panel,ksw2panel(reorient($ksw),900));

  }

  //! @ingroup fswphp
  public function test_fswText(){
    $this->assertTrue(fswText("M518x529S14c20481x471S27106503x489 M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468 S38800464x496"));
    $fsw = 'M528x518S15a07472x487S1f010490x503S26507515x483 M511x515S11e00488x485 S38800464x496 M559x596S2ff00482x482S30a00482x482S10e50539x487S15a1a532x473S22a04535x581S15a40536x548S15a48499x549S22a14499x581S26507519x505S10e00499x498 M523x524S11541499x497S1150a477x499S22a04500x476 M518x556S19220497x444S20320497x466S1f720492x484S19220497x501S1f720492x523S11502482x541 S38900464x493 L523x514S11541499x487S1150a477x489 L524x534S2e748482x509S10011500x465S2e704509x499S10019475x474 L563x524S2ff00482x482S19220539x476S15a1a536x460S2c601534x498S19200511x499 S38900464x493 M530x574S10030515x544S10038468x544S2a200504x524S2a218473x524S30a00482x482 M535x536S2ff00482x482S10002505x519S2ea00488x511 S38700463x496 M544x575S16d49459x524S28909486x519S18510519x520S14c13463x549S36d01479x494 M534x521S14c57508x494S2d800467x479 M520x518S15a37479x486S15357479x495S26507507x483 M525x515S1f710486x494S20302476x500S2ea08510x485 S38700463x496 M521x548S10041455x518S26501434x508S36d01479x494 M518x517S1f540503x493S26a01481x484 M535x552S36a00482x477S10e58492x522S2b701516x513S37c06515x538 M554x538S22b11446x504S15030501x507S34700482x482S15038474x507S22b11452x489S22b07524x489S22b07530x504 S38700463x496 M534x543S1dc59467x484S22a13478x518S20500491x507S20500466x532S15a39511x457 M570x518S32107482x483S15a37523x491S15a37547x493 M533x529S10009467x480S10002503x471S2b714481x505S2b705511x491 S38810463x495 M524x586S15a40510x559S15a48478x559S26600508x524S26610476x524S30a00482x482 S38700463x496 M516x523S10041484x493S2d608487x478 M567x584S15a48472x536S15a40517x535S29406523x566S29416472x568S30e00482x488S30124482x477 S38810463x495 M564x577S15a47514x554S15a49483x544S26607542x529S26617511x518S30a00482x482 S38700463x496 M534x574S26a07513x531S15a37496x551S15a51495x551S30e00482x488S30124482x477 M540x519S10011519x489S2ff00482x482S28108514x455 M572x518S32107482x483S15a37523x489S15a37549x490 S38810463x495 M563x524S2ff00482x482S19220539x476S15a1a536x460S2c601534x498S19200511x499S30a00482x482 M518x542S14410492x511S2ff00482x482S20600469x516 M526x520S10012496x480S18518475x486S2e700496x495 M519x553S19220498x465S17620498x488S2a20c481x447S11502483x508S11920492x527 S38800464x496';
    $frags = explode(' ',$fsw);
    foreach ($frags as $frag){
      $this->assertTrue(fswText($frag),$frag);
    }
  }

  //! @ingroup fswphp
  public function test_fswQuery(){
    $this->assertTrue(fswQuery("Q"));
    $this->assertTrue(fswQuery("QT"));
    $this->assertTrue(fswQuery("QR100t105"));
    $this->assertTrue(fswQuery("QTR105t10f"));
    $this->assertTrue(fswQuery("QR109t1a0R301t305"));
    $this->assertTrue(fswQuery("QS100uu"));
    $this->assertTrue(fswQuery("QTS1005f"));
    $this->assertTrue(fswQuery("QTS1005f500x500V20"));
  }

  //! @ingroup fswphp
  public function test_range2regex(){
    //see R2RTest.php for exhaustive testing...
    $this->assertEquals('100',range2regex(100,100));
    $this->assertEquals('((1[0-9a-d][0-9a-f])|(1e[0-2]))',range2regex('100','1e2',1));
  }

  //! @ingroup fswphp
  public function test_query2regex(){
    $re_f = query2regex("Q");
    $re_t = array('/[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);

    $re_f = query2regex("QT");
    $re_t = array('/(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);


    $re_f = query2regex("QR100t105");
    $re_t = array('/[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S10[0-5][0-5][0-9a-f][0-9]{3}x[0-9]{3}(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);

    $re_f = query2regex("QTR105t10f");
    $re_t = array('/(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S10[5-9a-f][0-5][0-9a-f][0-9]{3}x[0-9]{3}(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);

    $re_f = query2regex("QTR105t10f500x500");
    $re_t = array('/(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S10[5-9a-f][0-5][0-9a-f]((4[89][0-9])|(5[01][0-9])|(520))x((4[89][0-9])|(5[01][0-9])|(520))(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);


    $re_f = query2regex("QR109t1a0R301t305");
    $re_t = array('/[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S((10[9a-f])|(1[1-9][0-9a-f])|(1a0))[0-5][0-9a-f][0-9]{3}x[0-9]{3}(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $re_t[] = '/[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S30[1-5][0-5][0-9a-f][0-9]{3}x[0-9]{3}(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/';
    $this->assertEquals(2,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);
    $this->assertEquals($re_t[1],$re_f[1]);

    $re_f = query2regex("QS100uu");
    $re_t = array('/[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S100[0-5][0-9a-f][0-9]{3}x[0-9]{3}(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);

    $re_f = query2regex("QTS1005f");
    $re_t = array('/(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S1005f[0-9]{3}x[0-9]{3}(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);

    $re_f = query2regex("QTS1005f500x500V20");
    $re_t = array('/(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S1005f((4[89][0-9])|(5[01][0-9])|(520))x((4[89][0-9])|(5[01][0-9])|(520))(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);

    $re_f = query2regex("QTS1005f500x500V10");
    $re_t = array('/(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*S1005f((49[0-9])|(50[0-9])|(510))x((49[0-9])|(50[0-9])|(510))(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*/');
    $this->assertEquals(1,count($re_f));
    $this->assertEquals($re_t[0],$re_f[0]);
  }

  //!@ingroup fswphp
  public function test_query_displace(){
    $qs = 'QS10000500x500';
    $qsn = 'QS10000540x530';
    $this->assertEquals($qsn,query_displace($qs,40,30));
  }

  //!@ingroup fswphp
  public function test_query2displace(){
    $qs = 'QS10000500x500';
    $qsa = array();
    $qsa[] = 'QS10000460x460';
    $qsa[] = 'QS10000500x460';
    $qsa[] = 'QS10000540x460';
    $qsa[] = 'QS10000460x500';
    $qsa[] = 'QS10000540x500';
    $qsa[] = 'QS10000460x540';
    $qsa[] = 'QS10000500x540';
    $qsa[] = 'QS10000540x540';

    $this->assertEquals($qsa,query2displace($qs));
  }

  //!@ingroup fswphp
  public function test_query_results(){
    $text = 'M518x529S14c20481x471S27106503x489 M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468 S38800464x496 M518x529S14c20481x471S27106503x489';
    $query = 'Q';
    $words = query_results($query,$text);
    $this->assertEquals('M518x529S14c20481x471S27106503x489 M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468',implode($words,' '));
  }

  //!@ingroup fswphp
  public function test_query_counts(){
    $text = 'M518x529S14c20481x471S27106503x489 M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468 S38800464x496 M518x529S14c20481x471S27106503x489';
    $query = 'Q';
    $results = query_counts($query,$text);
    $this->assertEquals('M518x529S14c20481x471S27106503x489 M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468',implode($results[0],' '));
    $this->assertEquals(2,$results[1]['M518x529S14c20481x471S27106503x489']);
    $this->assertEquals(3,$results[2]);
    $query = 'QT';
    $results = query_counts($query,$text);
    $this->assertEquals(0,$results[2]);
  }

// msw.php
  //! @ingroup mswphp
  public function test_isVert(){
    $this->assertFalse(isVert('B'),"B");
    $this->assertTrue(isVert('L'),"L");
    $this->assertTrue(isVert('M'),"M");
    $this->assertTrue(isVert('R'),"R");
    $this->assertFalse(isVert('AB'),"AB");
    $this->assertTrue(isVert('AL'),"AL");
    $this->assertTrue(isVert('AM'),"AM");
    $this->assertTrue(isVert('AR'),"AR");
  }

  //! @ingroup mswphp
  public function test_reorient(){
    $this->assertEquals('B18x29S14c20n19xn29S271063xn11 B18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38802n4xn36',reorient('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4'));
    $this->assertEquals('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4',reorient('B18x29S14c20n19xn29S271063xn11 B18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38802n4xn36'));
  }
  
  //! @ingroup mswphp
  public function test_ksw2fsw(){
    $this->assertEquals('M518x529S14c20481x471S27106503x489 M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468 S38800464x496',ksw2fsw('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4'));
    $ksw = 'M61x55S1ce4718xn82S1ce49n11xn89S36d03n21xn55S26c0741xn97S26c176xn110S21b0026xn17S36d01n21x44S30a00n22x10S34000n22x10S1000016xn5';
    $fsw = ksw2fsw($ksw);
    $this->assertTrue(fswText($fsw),"conv problem?");
  }

  //! @ingroup mswphp
  public function test_fsw2ksw(){
    $this->assertEquals('M18x29S14c20n19xn29S271063xn11 M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32 S38800n36xn4',fsw2ksw('M518x529S14c20481x471S27106503x489 M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468 S38800464x496'));
  }

  //! @ingroup mswphp
  public function test_bsw2fsw(){
    $this->assertEquals('AS10000M580x580S10000484x484',bsw2fsw('1001301101201038508501301101207f07f0'));
  }

  //! @ingroup mswphp
  public function test_fsw2bsw(){
    $this->assertEquals('1001301101201038508501301101207f07f0',fsw2bsw('AS10000M580x580S10000484x484'));
  }

  //! @ingroup mswphp
  public function test_query2ksw(){
    $this->assertEquals('M21x30S1005f0x0',query2ksw('QTS1005f500x500V20'));
    $this->assertEquals('',query2ksw('QR109t1a0R301t305'));
    $this->assertEquals('',query2ksw('QR109t1a0500x500'));
  }

  //! @ingroup mswphp
  public function test_query2syms(){
    $this->assertEquals('S1005fS101uuS1025f',query2syms('QTS1005fS101uu500x500S1025fV20'));
  }

  //! @ingroup mswphp
  public function test_query2ranges(){
    $this->assertEquals('R105t10fR205t210',query2ranges('QTR105t10f500x500R205t210'));
  }

  //! @ingroup mswphp
  public function test_query2anywhere(){
    $this->assertEquals('QTS1005fS101uuS1025fV20',query2anywhere('QTS1005fS101uu500x500S1025fV20'));
    $this->assertEquals('QTR105t10fR205t210',query2anywhere('QTR105t10f500x500R205t210'));
  }
 
  //! @ingroup spl
  public function test_id2key(){
    $this->assertEquals('10000',id2key('01-01-001-01-01-01'));
  } 
  
  //! @ingroup spl
  public function test_key2id(){
    $this->assertEquals('01-01-001-01-01-01',key2id('S10000',1));
  } 

  //! @ingroup spl
  public function test_iswaName(){
    $this->assertEquals('Hands',iswaName("01","en"));
  }
}
?>
