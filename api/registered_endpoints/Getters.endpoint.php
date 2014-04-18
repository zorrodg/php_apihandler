<?php

if(!isset($dictionary)) $dictionary = new MethodDictionary();

new Getter($dictionary, "teams");
new Getter($dictionary, "teams/group");
new Getter($dictionary, "matches");