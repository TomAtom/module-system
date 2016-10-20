<?php

namespace System;

interface iObjectWithAuthorization {
  
  public function canBeViewedByUser($identity = null);
  
  public function canBeChangedByUser($identity = null );
  
}
