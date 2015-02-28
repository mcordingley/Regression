<?php

namespace mcordingley\Regression;

use callable;

/**
 * DataSeries
 * 
 * Represents a single series of data used in a regression. By convention, the
 * first value in the series is statically set to 1 to represent the idea of
 * "constancy". The coefficient that the regression returns for this idea of
 * invariance is the intercept of the regression. If not provided, then no
 * intercept is calculated.
 */
class DataSeries
{
    protected $data;
    protected $inverse;
    protected $link;
    
    /**
     * __construct
     * 
     * @param array|null $data Data to initialize the series with.
     * @param callable|null $link Function to linearize this data series. Defaults to the identity function.
     * @param callable|null $inverse Function to delinearize this data series. Defaults to the identity function.
     */
    public function __construct(array $data = null, callable $link = null, callable $inverse = null)
    {
        $this->data = $data;
        $this->link = $link ?: function($datum) { return $datum; };
        $this->inverse = $inverse ?: function($datum) { return $datum; };
    }
    
    /**
     * getData
     * 
     * @return array $data
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * getInverse
     * 
     * @return callable The inverse linking function used by this data series.
     */
    public function getInverse()
    {
        return $this->link;
    }
    
    /**
     * getLink
     * 
     * @return callable The linking function used by this data series.
     */
    public function getLink()
    {
        return $this->link;
    }
    
    /**
     * setData
     * 
     * @param array $data
     * @return self
     */
    public function setData(array $data)
    {
        $this->data = $data;
        
        return $this;
    }
    
    /**
     * setInverse
     * 
     * Sets the inverse link function, which is used to delinearize the data for
     * non-linear regression.
     * 
     * @param callable $inverse
     * @return self
     */
    public function setInverse(callable $inverse)
    {
        $this->inverse = $inverse;
        
        return $this;
    }
    
    /**
     * setLink
     * 
     * Sets the link function, which is used to linearize the data for
     * non-linear regression.
     * 
     * @param callable $link
     * @return self
     */
    public function setLink(callable $link)
    {
        $this->link = $link;
        
        return $this;
    }
    
    /**
     * getDesign
     * 
     * Transforms and returns the data in this series for inclusion into the
     * design matrix used in the regression.
     * 
     * @return array
     */
    public function getDesign()
    {
        return array_map($this->link, $this->data);
    }
}