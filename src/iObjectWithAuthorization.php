<?php

namespace System;

interface iObjectWithAuthorization {

  public function canBeViewedByUser(?\System\Identity $identity): bool;

  public function canBeChangedByUser(?\System\Identity $identity): bool;
}