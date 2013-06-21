<?php

namespace ChangeSet;

class ChangeSet
{
	private $newInstances;
	private $managedInstances;
	private $removedInstances;
	private $changeGenerator;
	
	public function __construct()
	{
		$this->newInstances = new \SplObjectStorage();
		$this->managedInstances = new \SplObjectStorage();
		$this->removedInstances = new \SplObjectStorage();
		$this->changeGenerator  = new ChangeGenerator();
	}
	
	public function add($object)
	{
		if (isset($this->newInstances[$object]) || isset($this->managedInstances[$object])) {
			// @todo maybe just silently ignore this and unset blindly?
			throw new \InvalidArgumentException('I ALREADY HAZ IT!');
		}
		
		unset($this->removedInstances[$object]);
		$this->newInstances[$object] = $this->changeGenerator->getChange($object);
	}
	
	public function register($object)
	{
		if (isset($this->managedInstances[$object])) {
			// @todo maybe just silently ignore this and unset blindly?
			throw new \InvalidArgumentException('I ALREADY HAZ IT!');
		}
		
		unset($this->newInstances[$object], $this->removedInstances[$object]);
		$this->managedInstances[$object] = $this->changeGenerator->getChange($object);
	}
	
	public function remove($object)
	{
		if (isset($this->removedInstances[$object])) {
			// @todo maybe just silently ignore this and unset blindly?
			throw new \InvalidArgumentException('I ALREADY HAZ IT!');
		}
		
		unset($this->newInstances[$object], $this->managedInstances[$object]);
		$this->removedInstances[$object] = $this->changeGenerator->getChange($object);
	}
	
	public function isTracking($object)
	{
		return isset($this->managedInstances[$object])
			|| isset($this->newInstances[$object])
			|| isset($this->removedInstances[$object]); // maybe should not check this?
	}
	
	public function clean()
	{
		$cleaned = new self();
		
		foreach ($this->managedInstances as $object) {
			$cleaned->managedInstances[$object] = $this->managedInstances->offsetGet($object)->getSnapshot();
		}
		
		foreach ($this->newInstances as $object) {
			$cleaned->managedInstances[$object] = $this->newInstances->offsetGet($object)->getSnapshot();
		}
		
		return $cleaned;
	}
	
	public function clear()
	{
		return new static();
	}
}