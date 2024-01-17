# Project Name

## Setup

1. Install Docker

   - For Windows or MacOS, download and install Docker Desktop from [Docker official website](https://www.docker.com/products/docker-desktop).
   - For Linux, you can use the package manager of your distro to install Docker. For example, on Ubuntu, you can use the following commands:
     ```bash
     sudo apt-get update
     sudo apt-get install docker-ce docker-ce-cli containerd.io
     ```
   - Verify the installation by running:
     ```bash
     docker --version
     ```

2. Clone the repository to your local machine.

   ```
   git clone https://github.com/raymaxess/38d81bd7-bac0-46df-ba5f-a8f99abc163c.git
   ```

3. Navigate to the project directory.

   ```
   cd 38d81bd7-bac0-46df-ba5f-a8f99abc163c
   touch .env
   ```

4. Create the directory `storage/app/data` if it does not exist.

   ```
   mkdir -p storage/app/data
   ```

5. Copy data
   ```
   cp _dev/*.json storage/app/data
   ```

## Generate Report Command

To generate a student report, run the following command:

```
docker-compose run app php artisan command:generate-report
```

When application is executed, it should take 2 values as input. Student ID and Report to generate (Either Diagnostic, Progress or Feedback)

```
Please enter the following
Student ID: student1
Report to generate (1 for Diagnostic, 2 for Progress, 3 for Feedback): <report-number-by-user>
```

#### Diagnostic report's sample output

```
Tony Stark recently completed Numeracy assessment on 16th December 2021 10:46 AM
He got 15 questions right out of 16. Details by strand given below:

Number and Algebra: 5 out of 5 correct
Measurement and Geometry: 7 out of 7 correct
Statistics and Probability: 3 out of 4 correct

```

#### Progress report's sample output

```
Tony Stark has completed Numeracy assessment 3 times in total. Date and raw score given below:

Date: 14th December 2019, Raw Score: 6 out of 16
Date: 14th December 2020, Raw Score: 10 out of 16
Date: 14th December 2021, Raw Score: 15 out of 16

Tony Stark got 9 more correct in the recent completed assessment than the oldest
```

#### Feedback report's sample output

```
Tony Stark recently completed Numeracy assessment on 16th December 2021 10:46 AM
He got 15 questions right out of 16. Feedback for wrong answers given below

Question: What is the 'median' of the following group of numbers 5, 21, 7, 18, 9?
Your answer: A with value 7
Right answer: B with value 9
Hint: You must first arrange the numbers in ascending order. The median is the middle term, which in this case is 9

```

## Running Tests

To run the tests, use the following command:

```
docker-compose run app php artisan test
```

## Clear cache
To clear the cache, use the following command:

```
docker-compose run app php artisan cache:clear
```