<?php
session_start();
session_unset();
session_destroy();

header("Location:index"); // o donde esté tu login
exit();
