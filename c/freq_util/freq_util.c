
#include "freq_util.h"




static float frequencies[32] = BB_FREQUENCIES;
static double theta = 0;


void freq_init() {
	
	static int flag = 0;
	
	if (flag) {
		
		return;
	}
    
    
#if 1
    
	printf("----------------------\n");
	
	int i, len;
	
	for (i=0, len = strlen(BB_CHARACTERS); i<len; ++i) {
		
		unsigned int freq = (unsigned int)floor(BB_BASEFREQUENCY * pow(BB_SEMITONE, i));
		frequencies[i] = freq;
        
	}
    
#endif
    
    
    flag = 1;
    
}


int freq_to_num(unsigned int f, int *n) {
	
    /*
     frequencies[0] = (unsigned int)floor(BB_BASEFREQUENCY * pow(BB_SEMITONE, 0));
     frequencies[31] = (unsigned int)floor(BB_BASEFREQUENCY * pow(BB_SEMITONE, 31));
     
     
     if (n != NULL &&
     f >= frequencies[0]-BB_THRESHOLD*pow(BB_SEMITONE, 0) &&
     f <= frequencies[31]+BB_THRESHOLD*pow(BB_SEMITONE, 31)) {
     
     unsigned int i;
     
     for (i=0; i<32; i++) {
     
     unsigned int freq = (unsigned int)floor(BB_BASEFREQUENCY * pow(BB_SEMITONE, i));
     frequencies[i] = freq;
     
     if (abs(frequencies[i] - f) <= BB_THRESHOLD*pow(BB_SEMITONE, i)) {
     //if (abs(frequencies[i] - f) <= BB_THRESHOLD) {
     *n = i;
     return 0;
     }
     }
     }
     */
    
    freq_init();
    
    
    if (n != NULL &&
        f >= frequencies[0]-BB_THRESHOLD &&
        f <= frequencies[31]+BB_THRESHOLD) {
        
        unsigned int i;
        
        for (i=0; i<32; i++) {
            
            if (abs(frequencies[i] - f) <= BB_THRESHOLD) {
                
                *n = i;
                return 0;
            }
        }
    }
    
	
    /*
     if (n!=NULL && f>freq_range[0].start && f<freq_range[31].end) {
     
     unsigned int i;
     
     for (i=0; i<32; i++) {
     
     if (f>freq_range[i].start && f<freq_range[i].end) {
     
     *n = i;
     return 0;
     }
     }
     }
     */
	
	return -1;
}

int num_to_char(int n, char *c) {
	
	if (c != NULL && n>=0 && n<32) {
		
		*c = BB_CHARACTERS[n];
		
		return 0;
	}
    
	return -1;
}

int char_to_num(char c, unsigned int *n) {
	
	if (n == NULL) return -1;
	
	*n = 0;
	
	if (c>=48 && c<=57) {
		
		*n = c - 48;
		
		return 0;
        
	} else if (c>=97 && c<=118) {
		
		*n = c - 87;
		
		return 0;
	}
	
	return -1;
}

int num_to_freq(int n, unsigned int *f) {
    
    freq_init();
	
	if (f != NULL && n>=0 && n<32) {
		
		//*f =  (unsigned int)floor(BB_BASEFREQUENCY * pow(BB_SEMITONE, n));
		*f =  (unsigned int)floor(frequencies[n]);
        
        
		return 0;
	}
	
	return -1;
}

int char_to_freq(char c, unsigned int *f) {
	
	unsigned int n;
	
	if (f != NULL && char_to_num(c, &n) == 0) {
		
		unsigned int ff;
		
		if (num_to_freq(n, &ff) == 0) {
			
			*f = ff;
			return 0;
		}
	}
	
	return -1;
}



// 一个频率对应的一组PCM的buffer
int encode_sound(unsigned int freq, float buffer[], size_t buffer_length) {
    
    
    const double amplitude = 0.25;
	double theta_increment = 2.0 * PI * freq / SAMPLE_RATE;
	int frame;
    
	for (frame = 0; frame < buffer_length; frame++) {
        
		buffer[frame] = sin(theta) * amplitude;
		theta += theta_increment;
		
        if (theta > 2.0 * PI) {
            
			theta -= 2.0 * PI;
		}
	}
    
    return 1;
}

void _medianfilter(const element* signal, element* result, int N)
{
    //   Move window through all elements of the signal
    for (int i = 1; i < N - 1; ++i)
    {
        //   Pick up window elements
        element window[3];
        for (int j = 0; j < 3; ++j)
            window[j] = signal[i - 1 + j];
        //   Order elements (only half of them)
        for (int j = 0; j < 2; ++j)
        {
            //   Find position of minimum element
            int min = j;
            for (int k = j + 1; k < 3; ++k)
                if (window[k] < window[min])
                    min = k;
            //   Put found minimum element in its place
            const element temp = window[j];
            window[j] = window[min];
            window[min] = temp;
        }
        //   Get result - the middle element
        result[i] = window[1];
    }
	
	result[0] = signal[0];
}