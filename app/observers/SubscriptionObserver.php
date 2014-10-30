<?php

  /**
   * Subscriptions Observer
   */

  class SubscriptionObserver {

    /**
     * Subscriptions on make
     */
    function make(Subscription $subscription) {

    }

    /**
     * Subscribe to events
     */
    function subscribe($events) {

      // Listen to made subscriptions
      $events->listen('subscription.make', 'SubscriptionObserver@make');

    }
  }