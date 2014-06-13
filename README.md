zf2-selligent
=============

ZF2 module for Selligent Individual API

Install

Add 'Selligent' to your modules array in application.config.php 
Copy selligent.global.php to config/autoload dir

Usage

// Selligent email marketing opt-in
$selligent = $this->selligent()->subscribe($recipient);

// Selligent email marketing opt-out
$selligent = $this->selligent()->unsubscribe($recipient);
