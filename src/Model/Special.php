<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use InvalidArgumentException;
use function Safe\sprintf;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Special implements SpecialInterface
{
    public const ACTION_TYPE_OFF = 'off';

    public const ACTION_TYPE_INCREASE = 'increase';

    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var string|null */
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

    /** @var DateTimeInterface|null */
    protected $startsAt;

    /** @var DateTimeInterface|null */
    protected $endsAt;

    /** @var bool */
    protected $enabled = false;

    /** @var Collection|SpecialRuleInterface[] */
    protected $rules;

    /** @var string */
    protected $actionType = self::ACTION_TYPE_OFF;

    /** @var float */
    protected $actionPercent = 0.0;

    /** @var ChannelInterface[]|Collection */
    protected $channels;

    public static function getActionTypes(): array
    {
        return [
            self::ACTION_TYPE_OFF,
            self::ACTION_TYPE_INCREASE,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getMultiplier(): float
    {
        switch ($this->getActionType()) {
            case self::ACTION_TYPE_OFF:
                return (100 - $this->getActionPercent()) / 100;
            case self::ACTION_TYPE_INCREASE:
                return (100 + $this->getActionPercent()) / 100;
            default:
                throw new InvalidArgumentException(sprintf(
                    "Unknown actionType '%s'. Expected one of: %s",
                    $this->getActionType(),
                    implode(' ,', self::getActionTypes())
                ));
        }
    }

    public function isSpecialActiveAt(DateTime $now): bool
    {
        return
            (null === $this->getStartsAt() || $now->getTimestamp() > $this->getStartsAt()->getTimestamp()) &&
            (null === $this->getEndsAt() || $now->getTimestamp() < $this->getEndsAt()->getTimestamp())
            ;
    }

    public function getChannelCodes(): array
    {
        return $this->channels->map(static function (ChannelInterface $channel) {
            return $channel->getCode();
        })->toArray();
    }

    public function __construct()
    {
        $this->createdAt = new DateTime();

        $this->rules = new ArrayCollection();
        $this->channels = new ArrayCollection();
    }

    public function __toString(): string
    {
        $name = $this->getName();

        if (null === $name) {
            return (string) $this->getId();
        }

        return $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority ?? -1;
    }

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    public function setExclusive(bool $exclusive): void
    {
        $this->exclusive = $exclusive;
    }

    public function getStartsAt(): ?DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(?DateTimeInterface $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    public function getEndsAt(): ?DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(?DateTimeInterface $endsAt): void
    {
        $this->endsAt = $endsAt;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function hasRules(): bool
    {
        return !$this->rules->isEmpty();
    }

    public function hasRule(SpecialRuleInterface $rule): bool
    {
        return $this->rules->contains($rule);
    }

    public function addRule(SpecialRuleInterface $rule): void
    {
        if (!$this->hasRule($rule)) {
            $rule->setSpecial($this);
            $this->rules->add($rule);
        }
    }

    public function removeRule(SpecialRuleInterface $rule): void
    {
        $rule->setSpecial(null);
        $this->rules->removeElement($rule);
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function setActionType(string $actionType): void
    {
        $this->actionType = $actionType;
    }

    public function getActionPercent(): float
    {
        return $this->actionPercent;
    }

    public function setActionPercent(float $actionPercent): void
    {
        $this->actionPercent = $actionPercent;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }
}
