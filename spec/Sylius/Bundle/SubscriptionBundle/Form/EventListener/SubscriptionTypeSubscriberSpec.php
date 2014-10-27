<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\SubscriptionBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Sylius\Component\Subscription\Model\SubscriptionInterface;
use Sylius\Component\Subscription\Model\SubscriptionItemInterface;

class SubscriptionTypeSubscriberSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\Form\EventListener\SubscriptionTypeSubscriber');
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    public function it_removes_zero_quantity_items_from_subscription(
        FormEvent $event,
        SubscriptionInterface $subscription,
        SubscriptionItemInterface $item,
        SubscriptionItemInterface $itemZeroQuantity,
        Form $form
    ) {
        $item->getQuantity()->willReturn(2);
        $itemZeroQuantity->getQuantity()->willReturn(0);

        $subscription->getItems()->willReturn(array(
            $item,
            $itemZeroQuantity
        ));

        $form->isValid()->willReturn(true);
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($subscription);

        $subscription->getItems()->shouldBeCalled();
        $subscription->removeItem($itemZeroQuantity)->shouldBeCalled();

        $this->onPostSubmit($event);
    }

    public function it_does_not_remove_items_when_form_is_invalid(
        FormEvent $event,
        SubscriptionInterface $subscription,
        SubscriptionItemInterface $item,
        SubscriptionItemInterface $itemZeroQuantity,
        Form $form
    ) {
        $item->getQuantity()->willReturn(2);
        $itemZeroQuantity->getQuantity()->willReturn(0);

        $subscription->getItems()->willReturn(array(
            $item,
            $itemZeroQuantity
        ));

        $form->isValid()->willReturn(false);
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($subscription);

        $subscription->getItems()->shouldNotBeCalled();
        $subscription->removeItem($itemZeroQuantity)->shouldNotBeCalled();

        $this->onPostSubmit($event);
    }
}
