//
//  bb_freq_util.h
//  BBSDK
//
//  Created by Littlebox on 13-5-6.
//  Copyright (c) 2013年 Littlebox. All rights reserved.
//

#ifndef __freq_util__
#define __freq_util__




#include <complex.h>
#include <math.h>
#include <stdbool.h>
#include <float.h>
#include <stddef.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>



#define PI                      3.1415926535897932384626433832795028841971               //定义圆周率值
#define SAMPLE_RATE             44100                                                    //采样频率

#define BB_SEMITONE 			1.05946311
#define BB_BASEFREQUENCY		1760
#define BB_CHARACTERS			"0123456789abcdefghijklmnopqrstuv"

#define BB_FREQUENCIES          {1765,1856,1986,2130,2211,2363,2492,2643,2799,2964,3243,3316,3482,3751,3987,4192,4430,4794,5000,5449,5598,5900,6262,6627,7004,7450,7881,8174,8906,9423,9948,10536}

#define BB_THRESHOLD            16

#define BB_HEADER_0             17
#define BB_HEADER_1             19


typedef float element;

//void freq_init();

int freq_to_num(unsigned int f, int *n);

int num_to_char(int n, char *c);

int char_to_num(char c, unsigned int *n);

int num_to_freq(int n, unsigned int *f);

int char_to_freq(char c, unsigned int *f);

void _medianfilter(const element* signal, element* result, int N);

int encode_sound(unsigned int freq, float buffer[], size_t buffer_length);

#endif /* __freq_util__ */




