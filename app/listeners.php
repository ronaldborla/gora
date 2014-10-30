<?php

  /**
   * Listeners
   */

  // Subscribe to reservation events
  Event::subscribe('ReservationObserver');
  // Subscribe to subscription events
  Event::subscribe('SubscriptionObserver');