<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use RuntimeException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class AddTimestampableIndicesSubscriber implements EventSubscriber
{
    /** @var array */
    private $classes;

    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @throws StringsException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $metadata = $event->getClassMetadata();

        foreach ($this->classes as $class) {
            if (!is_a($metadata->name, $class, true)) {
                continue;
            }

            self::addIndices($metadata);
        }
    }

    /**
     * @throws StringsException
     */
    private static function addIndices(ClassMetadata $metadata): void
    {
        $indices = [];

        $fields = ['createdAt', 'updatedAt'];
        foreach ($fields as $field) {
            if (!$metadata->hasField($field)) {
                throw new RuntimeException(sprintf('The class %s does not have a "%s" field', $metadata->name, $field));
            }

            $column = $metadata->getColumnName($field);

            $indices[] = [
                'columns' => [$column],
            ];
        }

        $metadata->table = array_merge_recursive([
            'indexes' => $indices,
        ], $metadata->table);
    }
}
