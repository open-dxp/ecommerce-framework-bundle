## Synonyms 
With OpenSearch so called synonym filters can be configured to further optimize search behavior. 
For details see [Synonym Token Filter and following pages](https://opensearch.org/docs/latest/analyzers/token-filters/index/)
at OpenSearch documentation. 

OpenDxp provides an out-of-the box integration to provide synonyms for the synonym filters of OpenSearch. 


#### Synonym Providers
Synonym providers are symfony services that implement the `SynonymProviderInterface`, load synonyms 
from a specific source and provide it for using them in OpenSearch synonym filters. 

Sources can be simple files, OpenDxp assets, OpenDxp data objects, database tables or what ever source is
needed. 
OpenDxp ships with a simple `FileSynonymProvider` that can be used right away. 

Besides the service configuration itself, the synonym providers need to be configured in index service 
configuration as follows. 

```yml
opendxp_ecommerce_framework:
    index_service:
        tenants:
            MyOpenSearchTenant:
                enabled: true
                worker_id: OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\OpenSearch\DefaultOpensearch
                config_id: OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config\OpenSearch

                config_options:
                    synonym_providers:
                        app_synonym_filter:
                            # service ID of synonym provider to use
                            provider_id: OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\SynonymProvider\FileSynonymProvider
                            # additional options for synonym provider (are applied to a child-service instance of given synonym provider
                            options:
                                synonymFile: '%kernel.project_dir%/public/var/assets/system/synonyms.txt'

```


#### Matching Synonym Providers to Synonym Filters
Matching between synonym providers and synonym filters in index settings takes place via name. In the sample below 
there the synonym provider `app_synonym_filter` provides the synonyms for the filter `app_synonym_filter`. 

The filter definition in the index settings can hold just an empty array (as some definition is required). During 
index building, index resetting or reloading of synonyms, the synonyms are loaded from synonyms provider and injected
to the synonyms array of the corresponding synonyms filter. 

```yml
opendxp_ecommerce_framework:
    index_service:
        tenants:
            MyOpenSearchTenant:
                enabled: true
                worker_id: OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\OpenSearch\DefaultOpensearch
                config_id: OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config\OpenSearch

                config_options:
                    synonym_providers:
                        app_synonym_filter:
                            # service ID of synonym provider to use
                            provider_id: OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\SynonymProvider\FileSynonymProvider
                            # additional options for synonym provider (are applied to a child-service instance of given synonym provider
                            options:
                                synonymFile: '%kernel.project_dir%/public/var/assets/system/synonyms.txt'

                    index_settings:
                        analysis:
                            filter:
                                app_synonym_filter: # mapping between synonym_provider and filter based on name
                                    type: synonym # or synonym_graph or any other ES type
                                    synonyms: [] # provide an empy array here, for ES6 [], for ES5[""] 

```

> Don't forget to also add the synonyms filter to an analysers filter array where needed - as described in [OpenSearch docs](https://opensearch.org/docs/latest/analyzers/token-filters/index/).


#### Updating Synonyms 
There are three ways of updating the synonyms in the OpenSearch index. For all applies that the synonyms are loaded 
from synonyms provider and injected to the synonyms array of the corresponding synonyms filter by OpenDxp.

##### Index Creation
During index creation the synonyms are loaded from synonym providers and applied to index. 

##### Reindex
During reindex the synonyms are loaded from synonym providers and applied to index when new index is created. 
> This is the recommended way to update synonyms during runtime at a production system as there is no service downtime. 

##### Search Index sync command
With the command `bin/console ecommerce:indexservice:search-index-sync update-synonyms` 
synonyms can be updated in the current index. For that, the index has to be closed and reopened which means a short downtime
of the index. 
