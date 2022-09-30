# PHP SDK for integrate with the Dermatology API offered by Legit.Health

Official SDK for integrate with the Dermatology API offered by Legit.Health 🩺🤖

## Instructions

If you want to start sending requests to Legit.Health's Dermatology API, you have to create an instance of the class `LegitHealth\Dapi\MediaAnalyzer`. It requires two arguments:

- The URL of the API. The preproduction enviroment uses the following value: `https://ia-pre.legit.health:8443/v2/legit_health/predict`.
- The API Key we have provided to you. Without it, you won't be able to send requests to the API.

The class `MediaAnalyzer` exposes two methods:

- `predict`, to send a predict request to the API, in case you need to analyze an image to obtain the probability of the detected pathologies.

- `followUp`, to send a follow up request to get the evolution information about a diagnosed image.

## Predict requests

The `predict` method of our `MediaAnalyzer` class receives one argument of the class `LegitHealth\Dapi\MediaAnalyzerArguments\PredictArguments`. The constructor of this class receives several arguments, so you can specify the image itself and information about the patient or the body site:

```php
$predictArguments = new PredictArguments(
    requestId: 'identifier of the request',
    content: base64_encode($image),
    bodySiteCode: BodySiteCode::ArmLeft,
    operator: Operator::Patient,
    subject: new Subject(
        'subject identifier',
        Gender::Male,
        '1.75',
        '69.00',
        DateTimeImmutable::createFromFormat('Ymd', '19861020'),
        'practitioner identifier'
        new Company('company identifier', 'Company Name')
    )
);
```

Once you've created a `PredictArguments` object, you can send the request in this way:

```php
$mediaAnalyzer = new MediaAnalyzer(
    $apiUrl,
    $apiKey
);
$response = $mediaAnalyzer->predict($predictArguments);
```

The response object contains several properties with the information returned by the API about the analyzed image:

- `preliminaryFindings` is an object of the class `LegitHealth\Dapi\MediaAnalyzerResponse\PreliminaryFindingsValue` with the probability of the different suspicions that the algorithm has about the image.

- `modality` is the modality of the image detected.

- `mediaValidity` is an object that contains information about whether the image sent contains relevant dermatological information

- `metricsValue` contains the sensitivity and specificity values.

- `conclusions` is an array of `Conclusion` objects with the detected pathologies and its probability. The total probability is distributed among each of the pathologies detected.

- `iaSeconds` is the time spent by the algorithms analyzying the image.

## Follow up requests

The `followUp` method of our `MediaAnalyzer` class receives one argument of the class `LegitHealth\Dapi\MediaAnalyzerArguments\FollowUpArguments`. The constructor of this class receives several arguments, so you can specify the image itself and information about a well known condition.

### Example. Follow up request for psoriasis

Let's see how to send a follow-up request for a patient diagnosed with psoriasis.

Firstly, we will create the different objects that represents the questionnaires used to track the evolution of psoriasis:

```php
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\ApasiLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\DlqiQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\PasiLocalQuestionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Pure4Questionnaire;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaires;

// ...

$apasiLocal = new ApasiLocalQuestionnaire(3);
$pasiLocal = new PasiLocalQuestionnaire(3, 2, 1, 1);
$pure4 = new Pure4Questionnaire(0, 0, 0, 1);
$dlqi = new DlqiQuestionnaire(1, 1, 2, 0, 0, 0, 1, 2, 2, 0);
$questionnaires = new Questionnaires([$apasiLocal, $pasiLocal, $pure4, $dlqi]);
```

Then, we will create an object of the class `LegitHealth\Dapi\MediaAnalyzerArguments\FollowUpArguments`:

```php
$followUpArguments = new FollowUpArguments(
    requestId: 'identifier of the request',
    content: base64_encode($image),
    pathologyCode: 'Psoriasis',
    bodySiteCode: BodySiteCode::ArmLeft,
    previousMedias: [
        new PreviousMedia(base64_encode($previousMediaImage), DateTimeImmutable::createFromFormat('Ymd', '20220106'))
    ],
    operator: Operator::Patient,
    subject: new Subject(
        'subject identifier',
        Gender::Male,
        '1.75',
        '69.00',
        DateTimeImmutable::createFromFormat('Ymd', '19861020'),
        'practitioner identifier'
        new Company('company identifier', 'Company Name')
    )
    scoringSystems: array_map(fn (Questionnaire $questionnaire) => $questionnaire->getName(), $questionnaires->questionnaires),
    // scoringSystems: ['APASI_LOCAL', 'PASI_LOCAL', 'PURE4', 'DLQI']
    questionnaires: $questionnaires
);
```

Unlike diagnostic support requests, follow-up requests supports the following additional arguments:

- `previousMedias` is an array of objects of the class `PreviousMedia` with a list of previous images taken of the current pathology.

- `scoringSystems` is an array of strings with the name of the scoring systems to be evaluated. It supports the following values:

```
[ ASCORAD_LOCAL, APASI_LOCAL, AUAS_LOCAL, AIHS4_LOCAL, DLQI, SCOVID, ALEGI, PURE4, UCT, AUAS7, APULSI, SCORAD_LOCAL, PASI_LOCAL, UAS_LOCAL, IHS4_LOCAL]
```

- `questionnaires` is an object of the class `LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaires` with the values of the scoring systems to be evaluated.

Once you've created a `PredictArguments` object, you can send the request in this way:

```php
$mediaAnalyzer = new MediaAnalyzer(
    $apiUrl,
    $apiKey
);
$response = $mediaAnalyzer->followUp($followUpArguments);
```

The response object contains several properties with the information returned by the API about the analyzed image:

- `preliminaryFindings` is an object of the class `LegitHealth\Dapi\MediaAnalyzerResponse\PreliminaryFindingsValue` with the probability of the different suspicions that the algorithm has about the image.

- `modality` is the modality of the image detected.

- `mediaValidity` is an object that contains information about whether the image sent contains relevant dermatological information

- `metricsValue` contains the sensitivity and specificity values.

- `iaSeconds` is the time spent by the algorithms analyzying the image.

Besides, it contains two extra properties:

- `explainabilityMedia`, with the image containing the surface of the injury detected by our AI algorithms.

- `scoringSystemsValues`, an object of the class `LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem\ScoringSystemValues.php` with the values calculated for each scoring system included in the array `scoringSystems` of the arguments.

#### The `ScoringSystemValues` object

The `ScoringSystemValues` contains all the information about a scoring system, for example, APASI_LOCAL.

You can access to the value of each facet using the method `getFacetCalculatedValue(string $facetCode)`.

By invoking the method `getFacets` you will get an array of facets. Each element in this list is an array with three keys:

- `facet`. The facet code.
- `value`. The calculated value for the facet. This value will be inside the allowed range for the facet.
- `intensity`. It represents the intensity of that facet in a scale from 0 to 100.

Finally, you can access to the score of the scoring system through its property `score`. It is an object with three properties:

- `category`, which represents the severity of the score.
- `calculatedScore`, for those scoring systems whose calculation depends on facets that are computed by our AI algorithm: APASI_LOCAL, APULSI, ASCORAD_LOCAL and AUAS_LOCAL.
- `questionnaire`, for those scoring systems whose calculations not depends on facet computed by our AI algorithm, for example, DLQI.

For example, if you want to extract the values for `APASI_LOCAL`, you can do the following:

```php
$apasiLocalScoringSystemValue = $response->getScoringSystemValues('APASI_LOCAL');

$apasiScore = $apasiLocalScoringSystemValue->getScore()->calculatedScore;
$apasiSeverityCategory = $apasiLocalScoringSystemValue->getScore()->category;
```

You can also extract the values of each calculated facet that make up the APASI:

```php
$apasiLocalScoringSystemValue = $response->getScoringSystemValues('APASI_LOCAL');
$desquamation = $apasiLocalScoringSystemValue->getFacetCalculatedValue('desquamation');
$desquamationValue = $desquamation->value; // A value between 0 and 4 as the PASI states
$desquamationIntensity = $desquamation->intensity; // A value between 0 and 100 reflecting the intensity of the desquamation
```
