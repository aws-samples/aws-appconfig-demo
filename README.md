# aws-appconfig-demo

This is a simple demo that showcases a common use case of AWS AppConfig.

## How to deploy

The infrastructure is defined as a set of nested CloudFormation templates. Just package and deploy the master template located in `templates/main.yaml`. You can do this easily with the AWS CLI:

```
# Create an S3 bucket for storing the processed templates, if you don't have
# one already (otherwise you can skip this)
aws s3 mb s3://<your-bucket-name>

# Package all the templates
aws cloudformation package \
	--template-file ./templates/main.yaml \
	--s3-bucket <your-bucket-name> \
	--output-template-file ./templates/processed.yaml

# And now deploy them
aws cloudformation deploy \
	--stack-name aws-appconfig-demo \
	--template-file ./templates/processed.yaml \
	--capabilities CAPABILITY_IAM
```

The stack output includes a link to the ALB endpoint.

## How to use

The template deploys an ECS service which runs a mock music API. Invoking the ALB (and thus the service) returns a list of music albums:

```
[
    {
        "artist": "Michael Jackson",
        "title": "Thriller"
    },
    {
        "artist": "Elton John",
        "title": "Leather Jackets"
    },
    {
        "artist": "The Beatles",
        "title": "Abbey Road"
    },
    {
        "artist": "The Whispers",
        "title": "The Whispers"
    }
]
```

Customers have asked that the API also returns the album release year. The development team in charge of this service has already implemented this, but since they work with [Feature Toggles](https://www.martinfowler.com/articles/feature-toggles.html) (a.k.a. Feature Flags), the API does not yet return that attribute. The last step is to enable the feature.

Instead of deploying a new version of the app (which might take some time, depending on the specifics of it), we'll just update the configuration file containing the flags and propagate them to the instances of the app, which are already running.

The template has created the necessary AppConfig resources. Let's just deploy a new configuration:

1. Go to the AppConfig console: https://eu-west-1.console.aws.amazon.com/systems-manager/appconfig/?region=eu-west-1 (ensure that the region is the one where you deployed the CloudFormation stack).
2. Click on the `aws-appconfig-demo` app to view its details.
3. Go to the `Configuration profiles` tab.
4. Click on the `app` profile.
5. Click on `Create` next to the **No hosted configuration versions exists for this configuration profile** message.
6. Select `JSON` as the content type, and enter the following as the content:

```
{
    "includeReleaseYear": true,
    "errorProbability": 0
}
```

7. Click on **Create hosted configuration version**.

Now let's deploy it:

1. Click on **Start deployment**.
2. Select the `prod` environment, version `1`, and deployment strategy `Custom.Immediate.Bake5Mins`.
3. Click on **Start deployment**.

The deployment will begin, and should be instantaneous. The demo app includes a script that runs every minute and checks if there are new configurations available. Wait for a minute and then refresh the app. The response should now include the release year:

```
[
    {
        "artist": "Michael Jackson",
        "title": "Thriller",
        "year": 1982
    },
    {
        "artist": "Elton John",
        "title": "Leather Jackets",
        "year": 1986
    },
    {
        "artist": "The Beatles",
        "title": "Abbey Road",
        "year": 1969
    },
    {
        "artist": "The Whispers",
        "title": "The Whispers",
        "year": 1979
    }
]
```

## What's next?

The demo is still a Work in Progress. Some of the things we're working on:

* Monitors and automated rollbacks.
* Deployments across a large fleet of containers with different strategies.

## Security

See [CONTRIBUTING](CONTRIBUTING.md#security-issue-notifications) for more information.

## License

This library is licensed under the MIT-0 License. See the LICENSE file.
