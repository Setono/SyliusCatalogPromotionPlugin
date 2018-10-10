<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * Class Special
 */
class Special implements SpecialInterface
{
    const ACTION_TYPE_OFF = 'off';
    const ACTION_TYPE_INCREASE = 'increase';

    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * When exclusive, promotion with top priority will be applied
     *
     * @var int
     */
    protected $priority = 0;

    /**
     * Cannot be applied together with other promotions
     *
     * @var bool
     */
    protected $exclusive = false;

    /**
     * @var \DateTimeInterface|null
     */
    protected $startsAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $endsAt;

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var Collection|SpecialRuleInterface[]
     */
    protected $rules;

    /**
     * @var string
     */
    protected $actionType = self::ACTION_TYPE_OFF;

    /**
     * @var int
     */
    protected $actionPercent = 0;

    /**
     * @var ChannelInterface[]|Collection
     */
    protected $channels;

    /**
     * @return array
     */
    public static function getActionTypes(): array
    {
        return [
            self::ACTION_TYPE_OFF,
            self::ACTION_TYPE_INCREASE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiplier(): float
    {
        switch ($this->getActionType()) {
            case self::ACTION_TYPE_OFF:
                return (100 - $this->getActionPercent()) / 100;
            case self::ACTION_TYPE_INCREASE:
                return (100 + $this->getActionPercent()) / 100;
            default:
                throw new \Exception(sprintf(
                    "Unknown actionType '%s'. Expected one of: %s",
                    $this->getActionType(),
                    implode(' ,', self::getActionTypes())
                ));
        }
    }

    /**
     * Special constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();

        $this->rules = new ArrayCollection();
        $this->channels = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority ?? -1;
    }

    /**
     * {@inheritdoc}
     */
    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    /**
     * {@inheritdoc}
     */
    public function setExclusive(bool $exclusive): void
    {
        $this->exclusive = $exclusive;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartsAt(?\DateTimeInterface $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndsAt(?\DateTimeInterface $endsAt): void
    {
        $this->endsAt = $endsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRules(): bool
    {
        return !$this->rules->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule(SpecialRuleInterface $rule): bool
    {
        return $this->rules->contains($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(SpecialRuleInterface $rule): void
    {
        if (!$this->hasRule($rule)) {
            $rule->setSpecial($this);
            $this->rules->add($rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(SpecialRuleInterface $rule): void
    {
        $rule->setSpecial(null);
        $this->rules->removeElement($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionType(): string
    {
        return $this->actionType;
    }

    /**
     * {@inheritdoc}
     */
    public function setActionType(string $actionType): void
    {
        $this->actionType = $actionType;
    }

    /**
     * {@inheritdoc}
     */
    public function getActionPercent(): int
    {
        return $this->actionPercent;
    }

    /**
     * {@inheritdoc}
     */
    public function setActionPercent(int $actionPercent): void
    {
        $this->actionPercent = $actionPercent;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }
}
