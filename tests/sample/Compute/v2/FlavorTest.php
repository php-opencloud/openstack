<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Compute\v2\Models\Flavor;

class FlavorTest extends TestCase
{
    public function testCreate(): Flavor
    {
        $name = $this->randomStr();

        /** @var $flavor \OpenStack\Compute\v2\Models\Flavor */
        require_once $this->sampleFile('flavors/create.php', ['{flavorName}' => $name]);

        $this->assertInstanceOf(Flavor::class, $flavor);
        $this->assertEquals($name, $flavor->name);

        return $flavor;
    }

    /**
     * @depends testCreate
     */
    public function testList(Flavor $createdFlavor)
    {
        $found = false;
        require_once $this->sampleFile(
            'flavors/list.php',
            [
                '/** @var \OpenStack\Compute\v2\Models\Flavor $flavor */' => <<<'PHP'
/** @var \OpenStack\Compute\v2\Models\Flavor $flavor */
if ($flavor->id === $createdFlavor->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testCreate
     */
    public function testRead(Flavor $createdFlavor)
    {
        /** @var \OpenStack\Compute\v2\Models\Flavor $flavor */
        require_once $this->sampleFile('flavors/read.php', ['{flavorId}' => $createdFlavor->id]);

        $this->assertInstanceOf(Flavor::class, $flavor);
        $this->assertEquals($createdFlavor->id, $flavor->id);
        $this->assertEquals($createdFlavor->name, $flavor->name);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Flavor $createdFlavor)
    {
        require_once $this->sampleFile('flavors/delete.php', ['{flavorId}' => $createdFlavor->id]);

        foreach ($this->getService()->listFlavors() as $flavor) {
            $this->assertNotEquals($createdFlavor->id, $flavor->id);
        }

        $this->expectException(BadResponseError::class);
        $createdFlavor->retrieve();
    }
}