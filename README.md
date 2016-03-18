# Symfony3-DatatableServerSide

## Initialization
register DatatableServerSide.php as a service to services.yml

```
datatable.server_side:
    class: AppBundle\Component\DatatableServerSide
    arguments: ["@doctrine.orm.entity_manager"]
```
Note: you can change the Service ID as you want

## Usage
use the service in controller

example in controller
```
use Symfony\Component\HttpFoundation\Request
// ...
/**
 * Route("get-data/")
 **/
public function getDataAction(Request $request)
{
    $dt = $this->get('datatable.server_side');
    $dt->setRepository('AppBundle:Post');
    $dt->setColumn(array('idCategory','title','body'));
    $dt->setRequest($request);
    $data = $dt->execute();
}
// ...
```

in ```$dt->setColumn()``` method, you have to send an array that contains attributes of you entity you want to display
