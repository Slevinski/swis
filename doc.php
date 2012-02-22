<?php
/*! \mainpage SignWriting Image Server manual



This manual is divided in the following sections:
- \subpage iswa
- \subpage csw

\section intro_sec Introduction

This is the introduction.

\section install_sec Installation

\subsection step1 Step 1: Opening the box

 * \par User defined paragraph:
 * Contents of the paragraph.

*/

//-----------------------------------------------------------

/*! \page iswa International SignWriting Alphabet
The ISWA 2010 is the symbol set for SWIS.
  - 7 Categories
  - 30 SymbolGrous
  - 652 BaseSymbols
  - 37,811 Symbols
  
\section symbol Symbol Codes and Keys
Each symbol has a unique 16-bit code.  

The symbol key is identified by the letter 'S' followed by 5 hexadecimal values.  
The first 3 hexadecimal values identify the BaseSymbol and this value is called the base.


\section ranges Symbol Ranges
It is possible to test the base of a symbol key.  We can tell if it isISWA(), isWrit(), isHand(), isMove(), isDyn(), isHead(), isTrunk(), isLimb(), isLoc(), isPunc(). 

\section uni Unicode

Now you can proceed to the \ref advanced "advanced section".
*/

//-----------------------------------------------------------

/*! \page csw Formal Character SignWriting
Formal strings use a sequential list of characters.
The idea of a character is the foundation of an encoding.
An encoding is a list of characters and a pattern.
The encoding of English is very simple with ASCII.
A word is a sequential list of letters with spaces to divide the words.
A list of words is aptly named a string.
Each word is a self contained whole.

The encoding of Modern SignWriting is based on the idea of words.  
Each word represents a sign of a sign language or a gesture of a visual performance.
The words of Modern SignWriting have two parts: the cluster and an optional prefix.
The cluster is a visual ordering of symbols in two dimensions.
The optional prefix is a sequential list used for temporal ordering.

The patterns of Modern SignWriting create the building blocks of the encoding.
How the blocks are written on a computer is varied but unified.  Either ASCII or PUA Unicode can be used.
These blocks have several possible representations and can be contracted to binary, strings of ASCII, or expanded to XML with Unicode.


\section bsw Binary SignWriting

This is the introduction.

\section install_sec Installation

\subsection step1 Step 1: Opening the box

 * \par User defined paragraph:
 * Contents of the paragraph.

Make sure you have first read about \ref iswa "the ISWA".
*/

/*! \page page1 A documentation page
  Leading text.
  \section sec An example section
  This page contains the subsections \ref subsection1 and \ref subsection2.
  For more info see page \ref page2.
  \subsection subsection1 The first subsection
  Text.
  \subsection subsection2 The second subsection
  More text.
*/

/*! \page page2 Another page
  Even more info.
*/

?>