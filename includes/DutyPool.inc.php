<?php

    class DutyPool
    {
        private $dutyEligiblePersonnel, $pool, $eligibleMean, $eligibleStdDev, $date;

        public function __construct($date)
        {
            $this->date = $date;
            
            //pull list of everyone
            try
			{
				$dbh = new PDO(DB_PATH);
			}
			catch(PDOException $e)
			{
				throw $e;
            }
            $this->dutyEligiblePersonnel = array();
            $query = $dbh->prepare("SELECT id FROM users WHERE ? BETWEEN dutyStartDate AND dutyEndDate");
            $query->execute(array($this->date));
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row)
            {
                $user = new user((int)$row["id"]);
                if($user->isDutyEligible($this->date))
                    array_push($this->dutyEligiblePersonnel, $user);
            }

            //we split them up into the experienced people and the newly posted in people

            // GOOD TO KNOW:
            // PHP's standard deviation calculation function requires the PECL extension.
            // Using the PECL function and implementing our own std dev calculation would
            // actually require about the same amount of code, so I decided to just
            // implement my own std dev calculator here.

            $totalForMean = 0;
            $newbieCount = 0;

            $experiencedPersonnel = $newlyPostedIn = array();
            foreach($this->dutyEligiblePersonnel as $user)
            {
                if($user->isNewlyPostedIn())
                {
                    array_push($newlyPostedIn, $user);
                    $newbieCount++;
                }
                else
                {
                    $totalForMean += $user->calculatePointPerDay($this->date);
                    array_push($experiencedPersonnel, $user);
                }
            }
            usort($experiencedPersonnel,array("user","comparePointPerDay"));
            
            $min = 0;
            if(count($experiencedPersonnel) > 0) $min = $experiencedPersonnel[0]->pointPerDay;
            
            if($newbieCount < 5)
            {
                $totalForMean += $newbieCount*$min;
                foreach($newlyPostedIn as $newbie)
                {
                    $newbie->pointPerDay = $min;
                }
            }
            else
            {
                foreach($newlyPostedIn as $newbie)
                {
                    $newbie->pointPerDay = 0;
                }
            }

            $this->dutyEligiblePersonnel = array_merge($newlyPostedIn,$experiencedPersonnel);

            $numDutyEligible = count($this->dutyEligiblePersonnel);

            if($numDutyEligible < 1)
                throw new Exception("There is no duty eligible personnel on ".$this->date."! Add some more people or get your NSFs to not chao geng so hard.");
            $cutoff = ceil($numDutyEligible * 0.15);
            $this->pool = array_slice($this->dutyEligiblePersonnel, 0, $cutoff);

            //now calculate the mean and stdDev of the eligible personnel
            $this->eligibleMean = $totalForMean/$numDutyEligible;

            $totalDistanceFromMeanSquared = 0;
            foreach($this->dutyEligiblePersonnel as $user)
            {
                $totalDistanceFromMeanSquared += pow( ($user->pointPerDay - $this->eligibleMean) , 2);
            }
            $this->eligibleStdDev = sqrt($totalDistanceFromMeanSquared/$numDutyEligible);
        }

        public function selectDutyPersonnel()
        {
            //var_dump($this->dutyEligiblePersonnel);
            if(count($this->pool) < 2) return $this->pool[0];
            
            if($this->eligibleStdDev < 0.00001)
            {
                return $this->pool[random_int(0,count($this->pool))];
            }

            $min = 99999.99;
            $minUser;
            //now we will handle desirability indexes
            foreach($this->pool as $user)
            {
                $user->desirabilityIndex = ($this->eligibleMean - $user->pointPerDay) / $this->eligibleStdDev * 10;

                //other adjustments can be made here
                
                $user->desirabilityIndex += (lcg_value() * 0.8) - 0.4;
                if($user->desirabilityIndex < $min)
                {
                    $min = $user->desirabilityIndex;
                    $minUser = $user;
                }
            }
            //var_dump($this->pool);
            return $minUser;
        }
    }

?>