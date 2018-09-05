<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;

class Special implements SpecialInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
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
     * @var \DateTimeInterface
     */
    protected $startsAt;

    /**
     * @var \DateTimeInterface
     */
    protected $endsAt;

    /**
     * @var Collection|SpecialRuleInterface[]
     */
    protected $rules;

    /**
     * @var string
     */
    protected $actionType;

    /**
     * @var integer
     */
    protected $actionPercent;

    /**
     * @var ChannelInterface[]|Collection
     */
    protected $channels;

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
    public function setExclusive(?bool $exclusive): void
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
     * @return string
     */
    public function getActionType(): string
    {
        return $this->actionType;
    }

    /**
     * @param string $actionType
     */
    public function setActionType(string $actionType): void
    {
        $this->actionType = $actionType;
    }

    /**
     * @return int
     */
    public function getActionPercent(): int
    {
        return $this->actionPercent;
    }

    /**
     * @param int $actionPercent
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
