# Configuration Reference

```yaml
prooph_service_bus:
    command_buses:
        foo_command_bus:
            router:
                async_switch: 'prooph_asynchronous_router.fooProducer'
        bar_command_bus:
            router:
                async_switch: 'prooph_asynchronous_router.barProducer'
    event_buses:
        foo_event_bus:
            router:
                async_switch: 'prooph_asynchronous_router.fooProducer'

prooph_asynchronous_router:
    producers:
        fooProducer:
            bridge: fooProducerServiceId
            routes:
              App\Namespace\FooMessage: foo_specific_routing_key
        barProducer:
            bridge: barProducerServiceId
            routes:
              bar_message: bar_specific_routing_key

```
